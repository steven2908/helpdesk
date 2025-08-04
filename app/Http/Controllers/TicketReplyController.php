<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\WAInboxController;

class TicketReplyController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
            'visibility' => 'nullable|in:public,internal',
        ]);

        $user = Auth::user();

        if (
            $ticket->user_id !== $user->id &&
            !$user->hasAnyRole(['admin', 'staff'])
        ) {
            abort(403, 'Unauthorized');
        }

        $reply = new TicketReply();
        $reply->ticket_id = $ticket->id;
        $reply->user_id = $user->id;
        $reply->message = $request->message;
        $reply->visibility = $request->input('visibility', 'public');

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('replies', $filename, 'public');
            $reply->attachment = $path;
        }

        $reply->save();

        // ======== BAGIAN PENGIRIMAN WHATSAPP ========
        $visibility = $reply->visibility;

        try {
            $wa = new WAInboxController();

            if ($visibility === 'public') {
                // Kirim ke pemilik tiket
                $ticketOwner = $ticket->user;
                $nomor = $ticketOwner->phone ?? null;

                if ($nomor) {
                    $nomor = ltrim($nomor, '+');

                    $pesan = "*Balasan Tiket Anda*\n\n"
                        . "🆔 *Tiket ID:* #{$ticket->ticket_id}\n"
                        . "💬 *Balasan:* {$reply->message}\n"
                        . "🧑‍💼 *dari :* {$reply->user->name}\n\n"
                        . "🔗 Silahkan login ke web untuk melihat detail tiket atau ketik *1* untuk membuat tiket baru atau *2* untuk chat cs.";

                    \Log::info('Mengirim notifikasi WA balasan tiket ke user', [
                        'user_id' => $ticket->user_id,
                        'nomor' => $nomor,
                        'ticket_id' => $ticket->ticket_id,
                    ]);

                    $wa->sendWAMessage($nomor, $pesan);
                }
            } elseif ($visibility === 'internal') {
                // Kirim ke semua user dengan role staff
                $staffUsers = User::role('staff')->get();

                foreach ($staffUsers as $staff) {
                    $nomor = $staff->phone ?? null;

                    if ($nomor) {
                        $nomor = ltrim($nomor, '+');

                        $pesan = "*Balasan Internal Tiket*\n\n"
                            . "🆔 *Tiket ID:* #{$ticket->ticket_id}\n"
                            . "👤 *Dari:* {$user->name}\n"
                            . "💬 *Pesan:* {$reply->message}\n\n"
                            . "🔒 Balasan ini bersifat *internal*. Silakan login untuk menindaklanjuti.";

                        \Log::info('Mengirim notifikasi WA internal ke staff', [
                            'staff_id' => $staff->id,
                            'nomor' => $nomor,
                            'ticket_id' => $ticket->ticket_id,
                        ]);

                        $wa->sendWAMessage($nomor, $pesan);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Gagal kirim WA notifikasi: " . $e->getMessage());
        }
        // ======== END BAGIAN WA ========

        if ($visibility === 'public') {
            \Log::info('Event TicketReplyCreatedTelegram akan dipanggil', [
                'reply_id' => $reply->id,
                'user_id' => $reply->user_id,
            ]);

            event(new \App\Events\TicketReplyCreatedTelegram($reply));
        } else {
            \Log::info('Tidak mengirim notifikasi Telegram karena visibility = internal', [
                'reply_id' => $reply->id,
                'user_id' => $reply->user_id,
            ]);
        }

        $ticket->touch();

        if ($user->hasRole('admin')) {
            $route = 'admin.tickets.show';
        } elseif ($user->hasRole('staff')) {
            $route = 'staff.tickets.show';
        } else {
            $route = 'tickets.show';
        }

        return redirect()
            ->route($route, $ticket->ticket_id)
            ->with('success', 'Balasan berhasil dikirim.');
    }
}
