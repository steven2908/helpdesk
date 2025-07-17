<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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

        // Authorization check
        if (
    $ticket->user_id !== $user->id &&
    !$user->hasAnyRole(['admin', 'staff'])
) {
    abort(403, 'Unauthorized');
}


        // Buat reply
        $reply = new TicketReply();
        $reply->ticket_id = $ticket->id;
        $reply->user_id = $user->id;
        $reply->message = $request->message;
        $reply->visibility = $request->input('visibility', 'public');

        // Cek & simpan lampiran
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('replies', $filename, 'public');
            $reply->attachment = $path; // simpan path ke kolom tunggal
        }

        $reply->save(); // Penting!


        \Log::info('Event TicketReplyCreatedTelegram akan dipanggil', [
    'reply_id' => $reply->id,
    'user_id' => $reply->user_id,
]);
        event(new \App\Events\TicketReplyCreatedTelegram($reply));

        $ticket->touch(); // update updated_at

        // Redirect sesuai role
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
