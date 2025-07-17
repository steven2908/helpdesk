<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;

class TicketReplyController extends Controller
{

    public function index($ticket_id)
{
    $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

    $replies = $ticket->replies()->with('user')->orderBy('created_at')->get();

    return response()->json([
        'ticket_id' => $ticket_id,
        'replies' => $replies,
    ]);
}


    public function store(Request $request, $ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

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
            return response()->json(['message' => 'Unauthorized'], 403);
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
        $ticket->touch(); // update updated_at

        return response()->json([
            'message' => 'Balasan berhasil dikirim.',
            'reply' => $reply
        ], 201);
    }
}
