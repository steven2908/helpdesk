<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user();
    $query = Ticket::with('user.company');

    // 🔒 Role-based filtering
    if (!$user->hasRole('admin')) {
        $query->where('user_id', $user->id);
    } else {
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
    }

    // 📌 Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 📌 Filter urgency
    if ($request->filled('urgency')) {
        $query->where('urgency', $request->urgency);
    }

    // 🔍 Search filter (subject or message)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%$search%")
              ->orWhere('message', 'like', "%$search%");
        });
    }

    // 📅 Filter by date range
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // 🔚 Pagination
    $tickets = $query->latest()->paginate(10);

    return response()->json($tickets);
}


    public function store(Request $request)
    {
        $user = $request->user();
        
        // 🛑 Cek role yang diperbolehkan
    if (!$user->hasAnyRole(['admin', 'user'])) {
        return response()->json([
            'message' => 'Unauthorized. Only admin and user can create tickets.'
        ], 403);
    }

        $rules = [
            'subject' => 'required',
            'message' => 'required',
            'urgency' => 'required|in:low,medium,high,urgent',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ];

        if ($user->hasRole('admin')) {
            $rules['client_id'] = 'required|exists:companies,id';
            $rules['sub_client_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        $ticket = Ticket::create([
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'urgency' => $validated['urgency'],
            'status' => 'open',
            'user_id' => $user->hasRole('admin') ? $validated['sub_client_id'] : $user->id,
            'company_id' => $user->hasRole('admin') ? $validated['client_id'] : $user->company_id,
            'attachment' => $path,
        ]);

        return response()->json(['message' => 'Tiket berhasil dibuat', 'ticket' => $ticket], 201);
    }

    public function show($id)
    {
        $ticket = Ticket::with('user.company')->findOrFail($id);
        $user = Auth::user();

        if (
            $ticket->user_id !== $user->id &&
            !($user->hasRole('admin') && $ticket->company_id === $user->company_id)
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($ticket);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);

        $user = $request->user();
        $ticket = Ticket::findOrFail($id);

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->status = $request->status;
        $ticket->save();

        return response()->json(['message' => 'Status berhasil diperbarui', 'ticket' => $ticket]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $ticket = Ticket::findOrFail($id);

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->delete();
        return response()->json(['message' => 'Tiket berhasil dihapus']);
    }
}
