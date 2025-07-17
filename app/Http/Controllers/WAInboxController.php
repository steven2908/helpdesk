<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Ticket, User, WASession};
use Illuminate\Support\Facades\Http;

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
        \Log::info("WA masuk dari $nomor: $pesan");

        $user = User::where('phone', $nomor)->first();
if (!$user) {
    // Abaikan user tidak terdaftar, jangan kirim pesan
    return response()->json(['status' => 'ignored', 'message' => 'Nomor tidak dikenal, tidak dibalas.']);
}


        $session = WASession::firstOrCreate(
            ['phone' => $nomor],
            ['step' => 'idle', 'data' => json_encode([])]
        );

        $step = $session->step;
        $data = json_decode($session->data, true);
        $pesanLower = strtolower($pesan);

        switch ($step) {
            case 'idle':
                if (in_array($pesanLower, ['help', 'bantuan'])) {
                    $this->sendWAMessage($nomor, "📖 *Daftar Perintah Tersedia:*\n\n"
                        . "🔹 *buat tiket* — Membuat tiket baru\n"
                        . "🔹 *list tiket* — Melihat daftar tiket Anda\n"
                        . "🔹 *help* — Melihat daftar perintah\n\n"
                        . "Silakan ketik salah satu perintah.");
                } elseif (in_array($pesanLower, ['buat tiket', 'buat tiket baru'])) {
                    $session->step = 'awaiting_subject';
                    $session->save();
                    $this->sendWAMessage($nomor, "📝 Silakan ketik *judul/subject* tiket Anda:");
                } elseif (in_array($pesanLower, ['list tiket', 'daftar tiket'])) {
                    $tickets = Ticket::where('user_id', $user->id)
                        ->latest()
                        ->take(5)
                        ->get();

                    if ($tickets->isEmpty()) {
                        $this->sendWAMessage($nomor, "📭 Anda belum memiliki tiket.");
                    } else {
                        $pesanList = "🎫 *Daftar Tiket Anda (maks 5 terakhir):*\n";
                        foreach ($tickets as $t) {
                            $pesanList .= "\n🆔 #{$t->ticket_id}\n📌 {$t->subject}\n📅 {$t->created_at->format('d M Y H:i')}\n📊 Status: {$t->status}\n⚠️ Urgensi: {$t->urgency}\n";
                        }
                        $this->sendWAMessage($nomor, $pesanList);
                    }
                } else {
                    $this->sendWAMessage($nomor, "👋 Selamat datang di Helpdesk!\n\nKetik salah satu:\n"
                        . "- *buat tiket* untuk membuat tiket\n"
                        . "- *list tiket* untuk melihat tiket Anda\n"
                        . "- *help* untuk melihat semua perintah");
                }
                break;

            case 'awaiting_subject':
                if (empty($pesan)) {
                    $this->sendWAMessage($nomor, "❌ Judul tidak boleh kosong. Silakan ketik subject tiket Anda:");
                    return;
                }

                $data['subject'] = $pesan;
                $session->step = 'awaiting_message';
                $session->data = json_encode($data);
                $session->save();

                $this->sendWAMessage($nomor, "✏️ Sekarang, ketik *pesan atau keluhan Anda*:");
                break;

            case 'awaiting_message':
                $data['message'] = $pesan;
                $session->step = 'awaiting_urgency';
                $session->data = json_encode($data);
                $session->save();

                $this->sendWAMessage($nomor, "⚠️ Mohon pilih tingkat urgensi tiket Anda:\n_low / medium / high / urgent_");
                break;

            case 'awaiting_urgency':
                $urgency = strtolower($pesan);
                $validUrgency = ['low', 'medium', 'high', 'urgent'];

                if (!in_array($urgency, $validUrgency)) {
                    $this->sendWAMessage($nomor, "⚠️ Urgensi tidak valid. Pilih salah satu: _low / medium / high / urgent_");
                    return;
                }

                $ticket = Ticket::create([
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'urgency' => $urgency,
                    'status' => 'open',
                    'user_id' => $user->id,
                    'company_id' => $user->company_id ?? null,
                ]);

                $this->sendWAMessage($nomor, "✅ Tiket berhasil dibuat!\n\n"
                    . "ID: #{$ticket->ticket_id}\n"
                    . "Subject: {$ticket->subject}\n"
                    . "Urgensi: {$urgency}");

                $session->step = 'idle';
                $session->data = json_encode([]);
                $session->save();
                break;

            default:
                $session->step = 'idle';
                $session->data = json_encode([]);
                $session->save();

                $this->sendWAMessage($nomor, "🔁 Sesi direset.\nUntuk memulai pembuatan tiket baru, silakan ketik: *buat tiket*");
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    protected function sendWAMessage($nomor, $pesan)
    {
        try {
            Http::post('http://localhost:3000/send-message', [
                'nomor' => $nomor,
                'pesan' => $pesan,
            ]);
        } catch (\Exception $e) {
            \Log::error("Gagal kirim pesan WA ke $nomor: " . $e->getMessage());
        }
    }
}
