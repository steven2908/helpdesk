<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Ticket, TicketReply, User, WASession, Survey};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class WAInboxController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string',
            'pesan' => 'required|string',
        ]);

        $nomor = ltrim($request->nomor, '+');
        $pesan = trim($request->pesan);
        $pesanLower = strtolower($pesan);
        \Log::info("WA masuk dari $nomor: $pesan");

        $session = WASession::firstOrCreate(
            ['phone' => $nomor],
            ['step' => 'idle', 'data' => json_encode([])]
        );

        $step = $session->step;
        $data = json_decode($session->data, true);
        $user = User::where('phone', $nomor)->first();

        if ($pesanLower === 'sudah') {
            // Berasal dari CS, bukan dari tiket selesai
            $session->step = 'survey_cs_q1';
            $session->data = json_encode([]);
            $session->save();
            return $this->sendWAMessage($nomor, "🙏 Terima kasih telah menghubungi CS.\n\n1. Bagaimana pelayanan customer service kami? (1–5)");
        }

        if ($step === 'manual') {
            if ($pesanLower === 'buat laporan keluhan') {
                if (!$user) {
                    $session->step = 'idle';
                    $session->save();
                    return $this->sendWAMessage($nomor, "❌ Nomor Anda belum terdaftar. Silakan ketik *register* untuk mendaftar.");
                }
                $session->step = 'awaiting_subject';
                $session->data = json_encode([]);
                $session->save();
                return $this->sendWAMessage($nomor, "🔁 Auto-respon diaktifkan kembali.\n\n📝 Silakan ketik *judul keluhan atau permintaan* Anda:");
            }
            return response()->json(['status' => 'ok', 'message' => 'manual mode']);
        }

        if (str_starts_with($step, 'survey_')) {
            return $this->handleSurveyStep($session, $user, $nomor, $pesan);
        }

        switch ($step) {
            case 'idle':
                if ($pesanLower === 'buat tiket') {
                    if (!$user) {
                        return $this->sendWAMessage($nomor, "❌ Nomor Anda belum terdaftar. Silakan ketik *register* untuk mendaftar.");
                    }
                    $session->step = 'awaiting_subject';
                    $session->save();
                    return $this->sendWAMessage($nomor, "📝 Silakan ketik *judul keluhan atau permintaan* Anda:");
                } elseif ($pesanLower === 'chat dengan customer service') {
                    $session->step = 'manual';
                    $session->save();
                    $namaUser = $user ? $user->name : 'Pengguna';
                    return $this->sendWAMessage($nomor, "🤝 *$namaUser*, Anda akan disambungkan ke layanan Chat dengan Customer Service JPN. Silakan sampaikan informasi yang diperlukan dan harap menunggu tanggapan tim CS.\n\n⏰ Jam layanan: Senin–Jumat, 08.00–17.00 WIB.\n\n📌 Untuk kembali ke sistem otomatis ketik *sudah*.");
                } elseif ($pesanLower === 'detail tiket') {
                    $session->step = 'awaiting_ticket_detail';
                    $session->save();
                    return $this->sendWAMessage($nomor, "🔎 Masukkan *ID tiket* yang ingin Anda lihat:");
                } elseif ($pesanLower == 'register') {
                    $session->step = 'register_name';
                    $session->save();
                    return $this->sendWAMessage($nomor, "🧾 Silakan ketik *nama lengkap* Anda:");
                } else {
                    return $this->sendWAButtons($nomor, "Selamat datang di *Helpdesk*! Silakan pilih layanan:", [
                    ['id' => 'buat tiket', 'title' => 'Buat Tiket'],
                    ['id' => 'chat dengan customer service', 'title' => 'Chat dengan Customer Service'],
                    ['id' => 'detail tiket', 'title' => 'Detail Tiket'],
                ]);

                }

            case 'register_name':
                $data['name'] = $pesan;
                $session->step = 'register_email';
                $session->data = json_encode($data);
                $session->save();
                return $this->sendWAMessage($nomor, "✉️ Masukkan *email* Anda:");

            case 'register_email':
                $data['email'] = $pesan;
                $session->step = 'register_password';
                $session->data = json_encode($data);
                $session->save();
                return $this->sendWAMessage($nomor, "🔒 Buat *password* Anda (minimal 6 karakter):");

            case 'register_password':
                if (strlen($pesan) < 6) {
                    return $this->sendWAMessage($nomor, "⚠️ Password terlalu pendek. Minimal 6 karakter.");
                }
                $data['password'] = bcrypt($pesan);
                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $nomor,
                    'password' => $data['password'],
                ]);
                $session->step = 'idle';
                $session->data = json_encode([]);
                $session->save();
                return $this->sendWAMessage($nomor, "✅ Registrasi berhasil! Silakan ketik *1* untuk buat tiket atau *2* untuk chat CS.");

            case 'awaiting_subject':
                if (empty($pesan)) return $this->sendWAMessage($nomor, "❌ Judul tidak boleh kosong.");
                $data['subject'] = $pesan;
                $session->step = 'awaiting_message';
                $session->data = json_encode($data);
                $session->save();
                return $this->sendWAMessage($nomor, "✏️ Ketik *isi keluhan atau permintaan* Anda:");

            case 'awaiting_message':
                $data['message'] = $pesan;
                $session->step = 'awaiting_urgency';
                $session->data = json_encode($data);
                $session->save();
                return $this->sendWAMessage($nomor, "⚠️ Pilih urgensi: _low / medium / high / urgent_");

            case 'awaiting_urgency':
                $urgency = strtolower($pesan);
                if (!in_array($urgency, ['low', 'medium', 'high', 'urgent'])) {
                    return $this->sendWAMessage($nomor, "⚠️ Pilihan tidak valid.");
                }

                $ticket = Ticket::create([
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'urgency' => $urgency,
                    'status' => 'open',
                    'user_id' => $user->id,
                    'company_id' => $user->company_id ?? null,
                ]);

                $this->sendWAMessage($nomor, "✅ Tiket dibuat!\n\nID: #{$ticket->ticket_id}\nSubject: {$ticket->subject}\nUrgensi: {$urgency}");

                foreach (User::role('staff')->whereNotNull('phone')->get() as $staff) {
                    $this->sendWAMessage($staff->phone, "📥 Tiket Baru dari {$user->name}:\n🆔 #{$ticket->ticket_id}\n📌 {$ticket->subject}\n📝 {$ticket->message}");
                }

                $session->step = 'idle';
                $session->data = json_encode([]);
                $session->save();
                return response()->json(['status' => 'ok']);

            case 'awaiting_ticket_detail':
                if (!preg_match('/^\d+$/', $pesan)) {
                    return $this->sendWAMessage($nomor, "❌ Masukkan angka ID tiket (contoh: 12345)");
                }
                $ticket = Ticket::where('ticket_id', $pesan)->where('user_id', $user->id)->first();
                if (!$ticket) {
                    $session->step = 'idle';
                    $session->save();
                    return $this->sendWAMessage($nomor, "❌ Tiket tidak ditemukan.");
                }

                $msg = "*Detail Tiket*\n🆔 #{$ticket->ticket_id}\n📌 {$ticket->subject}\n📝 {$ticket->message}\n⚠️ {$ticket->urgency}\n📅 {$ticket->created_at->format('d M Y H:i')}\n📌 Status: {$ticket->status}";
                foreach ($ticket->replies()->where('visibility', 'public')->get() as $reply) {
                    $msg .= "\n\n🔁 {$reply->user->name}:\n{$reply->message}";
                }

                $session->step = 'idle';
                $session->save();
                return $this->sendWAMessage($nomor, $msg);
        }

        return response()->json(['status' => 'ok']);
    }

    public function sendWAMessage($nomor, $pesan)
    {
        try {
            Http::post('http://localhost:3000/send-message', [
                'nomor' => $nomor,
                'pesan' => $pesan,
            ]);
        } catch (\Exception $e) {
            \Log::error("Gagal kirim WA ke $nomor: " . $e->getMessage());
        }
    }

    public function sendWAButtons($nomor, $pesan, $buttons)
    {
        $opsi = "";
        foreach ($buttons as $i => $btn) {
            $opsi .= "\n" . ($i + 1) . ". " . $btn['title'];
        }
        $this->sendWAMessage($nomor, $pesan . $opsi . "\n\n*Ketik opsi layanan yang anda mau*");
    }

    protected function handleSurveyStep($session, $user, $nomor, $pesan)
    {
        $data = json_decode($session->data, true);
        $step = $session->step;

        if (str_starts_with($step, 'survey_q')) {
            $index = (int)substr($step, -1);
            if (!in_array($pesan, ['1', '2', '3', '4', '5'])) {
                $info = $this->getSurveyTicketInfo($session);
                $questions = [
                    1 => "1. Responsivitas Tim (1–5)?",
                    2 => "2. Komunikasi & Koordinasi (1–5)?",
                    3 => "3. Sikap & Keramahan Tim (1–5)?",
                    4 => "4. Pengetahuan Teknis Tim (1–5)?",
                    5 => "5. Kepuasan Keseluruhan (1–5)?",
                ];
                $index = (int)substr($session->step, -1);
                $questionText = $questions[$index] ?? '';
                return $this->sendWAMessage($nomor, "⚠️ Maaf, Anda belum menyelesaikan survei.\n\n$info\n\n✏️ *Pertanyaan saat ini:*\n$questionText\n\n🙏 Silakan masukkan angka *1–5* untuk melanjutkan survei.");
            }


            $data["q$index"] = $pesan;
            if ($index < 5) {
                $session->step = "survey_q" . ($index + 1);
                $session->data = json_encode($data);
                $session->save();

                $questions = [
                    2 => "2. Komunikasi & Koordinasi? (1–5)",
                    3 => "3. Sikap & Keramahan Tim? (1–5)",
                    4 => "4. Pengetahuan Teknis Tim? (1–5)",
                    5 => "5. Kepuasan Keseluruhan? (1–5)",
                ];

                return $this->sendWAMessage($nomor, $questions[$index + 1]);
            }

            $session->step = 'survey_saran';
            $session->data = json_encode($data);
            $session->save();
            return $this->sendWAMessage($nomor, "📝 Mohon berikan *saran atau masukan tambahan*:");
        }

        if ($step === 'survey_cs_q1') {
    if (!in_array($pesan, ['1', '2', '3', '4', '5'])) {
        return $this->sendWAMessage($nomor, "❌ Jawab 1–5.");
    }
    $data['cs_q1'] = $pesan;
    $session->step = 'survey_cs_q2';
    $session->data = json_encode($data);
    $session->survey_type = 'cs'; // ✅ Tambahkan ini
    $session->save();
    return $this->sendWAMessage($nomor, "2. Apakah pelayanan CS baik? (1–5)");
}


        if ($step === 'survey_cs_q2') {
            if (!in_array($pesan, ['1', '2', '3', '4', '5'])) {
                return $this->sendWAMessage($nomor, "❌ Jawab 1–5.");
            }
            $data['cs_q2'] = $pesan;
            $session->step = 'survey_cs_q3';
            $session->data = json_encode($data);
            $session->save();
            return $this->sendWAMessage($nomor, "3. Seberapa puas Anda dengan CS kami? (1–5)");
        }

        if ($step === 'survey_cs_q3') {
            $data['cs_q3'] = $pesan;
            $session->step = 'survey_saran';
            $session->data = json_encode($data);
            $session->save();
            return $this->sendWAMessage($nomor, "📝 Mohon beri saran tambahan:");
        }

        if ($step === 'survey_saran') {
    $data['saran'] = $pesan;

    $survey = new Survey([
        'user_id' => $user->id ?? null,
        'nomor_pengirim' => $nomor,
        'saran'   => $data['saran'] ?? null,
    ]);

    // ⬅️ Cek apakah survei ini survei tiket atau CS
    if ($session->survey_type === 'cs') {
        $survey->cs_q1 = $data['cs_q1'] ?? null;
        $survey->cs_q2 = $data['cs_q2'] ?? null;
        $survey->cs_q3 = $data['cs_q3'] ?? null;
    } else {
        $survey->ticket_id = $data['ticket_id'] ?? null; // ⬅️ ini penting
        $survey->q1 = $data['q1'] ?? null;
        $survey->q2 = $data['q2'] ?? null;
        $survey->q3 = $data['q3'] ?? null;
        $survey->q4 = $data['q4'] ?? null;
        $survey->q5 = $data['q5'] ?? null;
    }

    $survey->save();

    // Reset session
    $session->step = 'idle';
    $session->data = json_encode([]);
    $session->survey_type = null; // ✅ clear tipe
    $session->save();

    return $this->sendWAMessage($nomor, "✅ Terima kasih! Survei Anda telah kami terima 🙏");
}


        return $this->sendWAMessage($nomor, "❌ Jawaban tidak dikenali.");
    }

    public function kirimSurveyJikaTiketSelesai(Ticket $ticket)
    {
        $user = $ticket->user;
        if (!$user || !$user->phone) return;
        $nomor = $user->phone;

        if (Survey::where('user_id', $user->id)
    ->where('ticket_id', $ticket->id) // Pastikan ada relasi
    ->exists()) return;


        $session = WASession::firstOrCreate(
            ['phone' => $nomor],
            ['step' => 'idle', 'data' => json_encode([])]
        );

        $session->step = 'survey_q1';
$session->data = json_encode(['ticket_id' => $ticket->id]);

        $session->save();

        $this->sendWAMessage($nomor, "✅ Tiket #{$ticket->ticket_id} telah *ditutup*.\n\n🙏 Mohon luangkan waktu untuk mengisi survei:\n\n1. Responsivitas Tim (1–5)?");
    }

    protected function getSurveyTicketInfo($session)
{
    $data = json_decode($session->data, true);
    if (isset($data['ticket_id'])) {
        $ticket = Ticket::find($data['ticket_id']);
        if ($ticket) {
            return "📌 Anda sedang mengisi survei untuk tiket:\n\n🆔 #{$ticket->ticket_id}\n📌 {$ticket->subject}\n📅 Dibuat: {$ticket->created_at->format('d M Y')}";
        }
    }
    return "📌 Anda sedang mengisi survei tiket.\nMohon selesaikan surveinya terlebih dahulu 🙏";
}

}
