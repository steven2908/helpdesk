<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Ticket, User, TelegramSession, TelegramLog, TicketReply};
use Illuminate\Support\Facades\Http;
use App\Events\TicketCreatedTelegram;
use Carbon\Carbon;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            \Log::info('Telegram webhook received', $update);

            TelegramLog::create([
                'chat_id' => $update['message']['chat']['id'] ?? null,
                'message' => $update['message']['text'] ?? null,
                'raw' => $update,
            ]);

            if (!isset($update['message'])) {
                return response()->json(['ok' => true]);
            }

            $chatId = $update['message']['chat']['id'];
            $text = strtolower(trim($update['message']['text'] ?? ''));
            $contact = $update['message']['contact']['phone_number'] ?? null;

            $session = TelegramSession::firstOrCreate(['chat_id' => $chatId]);

            if ($contact) {
                $normalized = ltrim($contact, '+');
                if (str_starts_with($normalized, '0')) {
                    $normalized = '62' . substr($normalized, 1);
                }

                $session->phone = $normalized;
                $session->save();

                $user = User::where('phone', $normalized)->first();
                if ($user) {
                    $user->telegram_chat_id = $chatId;
                    $user->save();
                    $session->state = 'idle';
                    $session->save();
                    return $this->sendTelegramEscapedMessage($chatId, "✅ Nomor diverifikasi. Kamu bisa ketik `buat tiket` untuk mulai.");
                } else {
                    return $this->sendTelegramEscapedMessage($chatId, "⚠️ Nomor ini tidak terdaftar. Silakan hubungi admin atau daftar terlebih dahulu.");
                }
            }

            if ($text === 'cancel') {
                $session->state = null;
                $session->data = null;
                $session->save();
                return $this->sendTelegramEscapedMessage($chatId, "❌ Sesi dibatalkan. Ketik `login`, `register`, atau `buat tiket` untuk memulai kembali.");
            }

            if ($text === 'reset') {
                $session->delete();
                return $this->sendTelegramEscapedMessage($chatId, "🔄 Sesi berhasil direset. Ketik `login`, `register`, atau `buat tiket` untuk memulai.");
            }

            if ($text === 'login') {
                $session->state = 'await_login_email';
                $session->save();
                return $this->sendTelegramEscapedMessage($chatId, "📧 Masukkan email akun Anda:");
            }

            if ($text === 'register') {
                $session->state = 'await_register_name';
                $session->save();
                return $this->sendTelegramEscapedMessage($chatId, "👤 Masukkan nama lengkap Anda:");
            }

            if ($text === '/help') {
                $user = User::where('telegram_chat_id', $chatId)->first();

                $commands = [
                    '-> login',
                    '-> register',
                    '-> reset session',
                    '-> cancel (untuk membatalkan sesi)',
                    '-> status login',
                    '-> buat tiket',
                    '-> lihat daftar tiket saya',
                    '-> status tiket #ID',
                ];

                if ($user && $user->hasRole('admin')) {
                    $commands[] = '-> list tiket hari ini';
                    $commands[] = '-> semua tiket';
                    $commands[] = '-> update status #ID menjadi STATUS';
                    $commands[] = '-> balas tiket #ID isi balasan';
                }

                $msg = "📖 *Daftar Perintah Tersedia:*\n\n" . implode("\n", $commands);
                return $this->sendTelegramEscapedMessage($chatId, $msg);
            }

            switch ($session->state) {
                case 'await_login_email':
                    $session->data = ['email' => $text];
                    $session->state = 'await_login_phone';
                    $session->save();
                    return $this->sendTelegramEscapedMessage($chatId, "📱 Masukkan *nomor HP* Anda (contoh: 081234567890):");

                case 'await_login_phone':
                    $email = $session->data['email'] ?? null;
                    $phone = ltrim($text, '+');
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }

                    $user = User::where('email', $email)->where('phone', $phone)->first();

                    if (!$user) {
                        $session->state = null;
                        $session->data = null;
                        $session->save();
                        return $this->sendTelegramEscapedMessage($chatId, "❌ Login gagal. Email atau nomor HP tidak cocok. Ketik `login` untuk mencoba lagi.");
                    }

                    $user->telegram_chat_id = $chatId;
                    $user->save();

                    $session->state = 'idle';
                    $session->save();

                    return $this->sendTelegramEscapedMessage($chatId, "✅ Login berhasil. Halo, *" . $this->escapeMarkdownV2($user->name) . "*.\nKetik `buat tiket` untuk mulai.");

                case 'await_register_name':
    $session->data = ['name' => $text];
    $session->state = 'await_register_email';
    $session->save();
    return $this->sendTelegramEscapedMessage($chatId, "📧 Masukkan email Anda:");

case 'await_register_email':
    $data = $session->data;
    $data['email'] = $text;
    $session->data = $data;
    $session->state = 'await_register_phone';
    $session->save();
    return $this->sendTelegramEscapedMessage($chatId, "📱 Masukkan nomor HP Anda (contoh: 081234567890):");

case 'await_register_phone':
    $data = $session->data;
    $phone = ltrim($text, '+');
    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }

    if (User::where('email', $data['email'])->orWhere('phone', $phone)->exists()) {
        $session->state = null;
        $session->data = null;
        $session->save();
        return $this->sendTelegramEscapedMessage($chatId, "❌ Email atau nomor HP sudah terdaftar. Silakan login atau gunakan data lain.");
    }

    $data['phone'] = $phone;
    $session->data = $data;
    $session->state = 'await_register_company';
    $session->save();

    return $this->sendTelegramEscapedMessage($chatId, "🏢 Masukkan *nama perusahaan* Anda (boleh yang baru):");

case 'await_register_company':
    $data = $session->data;

$companyName = trim($update['message']['text']);
$company = \App\Models\Company::firstOrCreate(
    ['name' => $companyName],
    ['description' => 'Didaftarkan via Telegram']
);


    $data['company_id'] = $company->id;
    $session->data = $data;
    $session->state = 'await_register_password';
    $session->save();

    return $this->sendTelegramEscapedMessage($chatId, "🔐 Masukkan *password* yang ingin Anda gunakan (min 6 karakter):");

case 'await_register_password':
    $data = $session->data;

    if (strlen($text) < 6) {
        return $this->sendTelegramEscapedMessage($chatId, "❌ Password minimal 6 karakter. Silakan coba lagi:");
    }

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'company_id' => $data['company_id'],
        'password' => bcrypt($text),
        'telegram_chat_id' => $chatId,
    ]);
    $user->assignRole('user');

    $company = \App\Models\Company::find($data['company_id']);

    $session->state = 'idle';
    $session->data = null;
    $session->save();

    $rawMessage = "✅ Registrasi berhasil. Selamat datang, *" . $user->name . "* dari *" . $company->name . "*!\nKetik `buat tiket` untuk mulai.";
return $this->sendTelegramEscapedMessage($chatId, $rawMessage);





                case 'await_subject':
                    $session->data = ['subject' => $text];
                    $session->state = 'await_description';
                    $session->save();
                    return $this->sendTelegramEscapedMessage($chatId, "📄 Silakan masukkan *deskripsi lengkap* dari masalah Anda:");

                case 'await_description':
                    $data = $session->data ?? [];
                    $data['description'] = $text;
                    $session->data = $data;
                    $session->state = 'await_urgency';
                    $session->save();
                    return $this->sendTelegramEscapedMessage($chatId, "⚠️ Pilih *tingkat urgensi*: low / medium / high / urgent");

                case 'await_urgency':
    $valid = ['low', 'medium', 'high', 'urgent'];
    if (!in_array(strtolower($text), $valid)) {
        return $this->sendTelegramEscapedMessage($chatId, "⚠️ Mohon pilih salah satu: low / medium / high / urgent");
    }

    $data = $session->data ?? [];
    $data['urgency'] = strtolower($text);
    $session->data = $data;

    $user = User::where('telegram_chat_id', $chatId)->first();
    if ($user) {
        // langsung buat tiket jika sudah login
        return $this->handleCompleteTicket($chatId, $session);
    } else {
        // belum login → minta nomor HP untuk verifikasi dulu
        $session->state = 'await_verification_phone';
        $session->save();
        return $this->sendTelegramEscapedMessage($chatId, "📱 Masukkan nomor HP kamu untuk verifikasi kalau kamu memang sudah punya akun (contoh: 081234567890):");
    }

    case 'await_verification_phone':
    $phone = ltrim($text, '+');
    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }

    $user = User::where('phone', $phone)->first();

    if (!$user) {
        return $this->sendTelegramEscapedMessage($chatId, "❌ Nomor tidak ditemukan. Silahkan masukkan nomor lain atau ketik 'register' untuk mendaftar, dan `cancel` untuk membatalkan sesi buat tiket.");
    }

    $user->telegram_chat_id = $chatId;
    $user->save();

    return $this->handleCompleteTicket($chatId, $session);



                case null:
                case 'idle':
                    return $this->handleIdleCommand($chatId, $text);

                default:
                    $session->state = null;
                    $session->data = null;
                    $session->save();
                    return $this->sendTelegramEscapedMessage($chatId, "❌ Terjadi kesalahan. Mulai ulang dengan ketik: `buat tiket`");
            }
        } catch (\Throwable $e) {
            \Log::error('Telegram webhook ERROR: ' . $e->getMessage());
            return response()->json(['error' => 'internal server error'], 500);
        }
    }

    protected function handleCompleteTicket($chatId, $session)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (!$user) {
            return $this->sendTelegramEscapedMessage($chatId, "⚠️ Kamu belum login. Ketik `login`.");
        }

        $data = $session->data ?? [];

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'subject' => $data['subject'] ?? '[No Subject]',
            'message' => $data['description'] ?? '-',
            'urgency' => $data['urgency'] ?? 'low',
            'status' => 'open',
        ]);

        event(new TicketCreatedTelegram($ticket));

        $session->state = 'idle';
        $session->data = null;
        $session->save();

        return $this->sendTelegramEscapedMessage($chatId, "✅ Tiket berhasil dibuat dengan ID: *#{$ticket->ticket_id}*");
    }

    protected function handleIdleCommand($chatId, $text)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();

        // admin commands
        if ($user && $user->hasRole('admin')) {
            if ($text === 'list tiket hari ini') {
                $today = Carbon::now('Asia/Jakarta')->toDateString();
                $tickets = Ticket::whereDate('created_at', $today)->latest()->get();

                if ($tickets->isEmpty()) {
                    return $this->sendTelegramEscapedMessage($chatId, "📭 Tidak ada tiket hari ini.");
                }

                $msg = "📅 *Tiket Hari Ini:*\n";
                foreach ($tickets as $t) {
                    $msg .= "• #{$t->ticket_id} - {$t->subject} ({$t->status})\n";
                }

                return $this->sendTelegramEscapedMessage($chatId, $msg);
            }

            if ($text === 'semua tiket') {
                $tickets = Ticket::latest()->take(10)->get();
                if ($tickets->isEmpty()) {
                    return $this->sendTelegramEscapedMessage($chatId, "📬 Belum ada tiket.");
                }

                $msg = "📋 *Semua Tiket Terbaru:*\n";
                foreach ($tickets as $t) {
                    $msg .= "• #{$t->ticket_id} - {$t->subject} ({$t->status})\n";
                }

                return $this->sendTelegramEscapedMessage($chatId, $msg);
            }

            if (str_starts_with($text, 'update status')) {
                if (!preg_match('/update status\s+(#?)([A-Za-z0-9\-]+)\s+menjadi\s+(open|in_progress|closed)/i', $text, $matches)) {
                    return $this->sendTelegramEscapedMessage($chatId, "⚠️ Format salah. Contoh:\n`update status 00123 menjadi open`");
                }

                $ticketId = $matches[2];
                $newStatus = strtolower($matches[3]);


                $ticket = Ticket::where('ticket_id', $ticketId)->first();
if (!$ticket) {
    return $this->sendTelegramEscapedMessage($chatId, "❌ Tiket dengan ID `#{$ticketId}` tidak ditemukan.");
}

$ticket->status = $newStatus;

/// === SLA RESPONSE TIME ===
if ($newStatus === 'in_progress' && !$ticket->response_at) {
    $ticket->response_at = now();

    $responseSla = $ticket->company->sla_response_time ?? 240; // default 4 jam
    $limitResponseTime = $ticket->created_at->copy()->addMinutes($responseSla);

    $isLate = $ticket->response_at->greaterThan($limitResponseTime);
    $slaResponseStatus = $isLate ? 'Terlambat' : 'Tepat Waktu';

    \Log::info("SLA Response status tiket #{$ticket->ticket_id}: {$slaResponseStatus}");
    // Atau kamu bisa tambahkan ke notifikasi kalau mau
}

// === SLA RESOLUTION TIME ===
if ($newStatus === 'closed') {
    $ticket->solved_at = now();

    $resolutionSla = $ticket->company->sla_resolution_time ?? 1440; // default 24 jam
    $limitSolveTime = $ticket->created_at->copy()->addMinutes($resolutionSla);

    $isLate = $ticket->solved_at->greaterThan($limitSolveTime);
    $slaResolutionStatus = $isLate ? 'Terlambat' : 'Tepat Waktu';

    \Log::info("SLA Resolution status tiket #{$ticket->ticket_id}: {$slaResolutionStatus}");
    // Atau tambahkan ke pesan notifikasi juga jika mau
}

$ticket->save();



                $notif = "📢 *Update Status Tiket*\n\n";
                $notif .= "• ID: #{$ticket->ticket_id}\n";
                $notif .= "• Status baru: *{$newStatus}*\n";
                $notif .= "• Diperbarui oleh: *{$user->name}*";

                if ($ticket->user && $ticket->user->telegram_chat_id) {
    $this->sendTelegramEscapedMessage($ticket->user->telegram_chat_id, $notif);

    // Tambahan notifikasi untuk user
    if ($newStatus === 'in_progress') {
        $this->sendTelegramEscapedMessage($ticket->user->telegram_chat_id,
            "🔧 Tiket kamu dengan ID *#{$ticket->ticket_id}* sedang kami proses. Harap tunggu ya 😊"
        );
    }

    if ($newStatus === 'closed') {
        $this->sendTelegramEscapedMessage($ticket->user->telegram_chat_id,
            "✅ Tiket kamu dengan ID *#{$ticket->ticket_id}* telah selesai ditangani. Terima kasih atas kesabarannya 🙏"
        );
    }
}


                return $this->sendTelegramEscapedMessage($chatId, "✅ Status tiket berhasil diperbarui dan notifikasi dikirim.");
            }

            if (str_starts_with($text, 'balas tiket')) {
                if (!preg_match('/balas tiket\s+(#?)([A-Za-z0-9\-]+)\s+(.+)/i', $text, $matches)) {
                    return $this->sendTelegramEscapedMessage($chatId, "⚠️ Format salah. Contoh:\n`balas tiket 00123 Isi pesan`");
                }

                $ticketId = $matches[2];
                $replyText = $matches[3];


                $ticket = Ticket::where('ticket_id', $ticketId)->first();
                if (!$ticket) {
                    return $this->sendTelegramEscapedMessage($chatId, "❌ Tiket dengan ID `#{$ticketId}` tidak ditemukan.");
                }

                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'message' => $replyText,
                    'visibility' => 'public',
                ]);

                $ticket->touch();

                if ($ticket->user && $ticket->user->telegram_chat_id) {
                    $msg = "📬 *Balasan Tiket #{$ticket->ticket_id}*\n\n";
                    $msg .= $replyText . "\n\n_Dibalas oleh: {$user->name}_";
                    $this->sendTelegramEscapedMessage($ticket->user->telegram_chat_id, $msg);
                }

                return $this->sendTelegramEscapedMessage($chatId, "✅ Balasan berhasil dikirim.");
            }
        }

        // user & public commands
        if ($text === 'buat tiket') {
            $session = TelegramSession::firstOrCreate(['chat_id' => $chatId]);
            $session->state = 'await_subject';
            $session->save();
            return $this->sendTelegramEscapedMessage($chatId, "📝 Baik. Silakan ketik *subject* tiketnya:");
        }

        if ($text === 'lihat daftar tiket saya') {
            if (!$user) {
                return $this->sendTelegramEscapedMessage($chatId, "⚠️ Kamu belum login. Ketik `login` atau `register` dulu.");
            }

            $tickets = Ticket::where('user_id', $user->id)->latest()->take(10)->get();
            if ($tickets->isEmpty()) {
                return $this->sendTelegramEscapedMessage($chatId, "📬 Kamu belum pernah membuat tiket.");
            }

            $msg = "🎫 *Tiket Kamu:*\n";
            foreach ($tickets as $t) {
                $msg .= "• #{$t->ticket_id} - {$t->subject} ({$t->status})\n";
            }

            return $this->sendTelegramEscapedMessage($chatId, $msg);
        }

        if ($text === 'status login') {
            if ($user) {
                return $this->sendTelegramEscapedMessage($chatId, "✅ Kamu sudah login sebagai *{$user->name}* ({$user->email})");
            } else {
                return $this->sendTelegramEscapedMessage($chatId, "⚠️ Kamu belum login. Ketik `login` atau `register`.");
            }
        }

        if (str_starts_with($text, 'status tiket')) {
            preg_match('/status tiket\s+(#?)([A-Za-z0-9\-]+)/i', $text, $matches);
            $ticketId = $matches[2] ?? null;


            if (!$ticketId) {
                return $this->sendTelegramEscapedMessage($chatId, "⚠️ Format salah. Contoh: `status tiket 00123`");
            }

            $ticket = Ticket::where('ticket_id', $ticketId)->first();
            if (!$ticket) {
                return $this->sendTelegramEscapedMessage($chatId, "❌ Tiket dengan ID `#{$ticketId}` tidak ditemukan.");
            }

            $msg  = "📄 *Status Tiket*\n\n";
            $msg .= "• ID: #{$ticket->ticket_id}\n";
            $msg .= "• Dibuat oleh: {$ticket->user->name}\n";
            $msg .= "• Subject: {$ticket->subject}\n";
            $msg .= "• Status: *{$ticket->status}*\n";
            $msg .= "• Urgensi: {$ticket->urgency}\n";
            $msg .= "• Dibuat: " . $ticket->created_at->format('d M Y H:i') . "\n";
            $msg .= "• Company: _" . ($ticket->company->name ?? '-') . "_";

            return $this->sendTelegramEscapedMessage($chatId, $msg);
        }

        return $this->sendTelegramEscapedMessage($chatId, "Ketik `/help` untuk melihat daftar perintah.");
    }

    protected function sendTelegramMessage($chatId, $message, $extra = [])
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        $payload = array_merge([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
        ], $extra);

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

        if (!$response->ok()) {
            \Log::error('Gagal kirim pesan Telegram:', [
                'chat_id' => $chatId,
                'response' => $response->body(),
            ]);
        }
    }

    protected function sendTelegramEscapedMessage($chatId, $message, $extra = [])
    {
        $escaped = $this->escapeMarkdownV2($message);
        return $this->sendTelegramMessage($chatId, $escaped, $extra);
    }

    protected function escapeMarkdownV2($text)
    {
        $escape = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        foreach ($escape as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        return $text;
    }
}
