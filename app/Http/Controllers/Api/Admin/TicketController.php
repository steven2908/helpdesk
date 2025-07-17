<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\SlaHelper;
use App\Helpers\TelegramHelper;


class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user.company']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
    $search = $request->search;
    $normalizedSearch = ltrim(preg_replace('/[^0-9]/', '', $search), '0');

    $query->where(function ($q) use ($search, $normalizedSearch) {
        $q->where('ticket_id', 'like', "%{$search}%")
            ->orWhere('ticket_id', 'like', "%{$normalizedSearch}%")
            ->orWhere('subject', 'like', "%{$search}%")
            ->orWhere('message', 'like', "%{$search}%")
            ->orWhereHas('user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
    });
}


        if ($request->filled('company_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('sla_status') && in_array($request->sla_status, ['on_time', 'late'])) {
            $query->whereNotNull('solved_at')
                ->whereHas('user.company', function ($q) {
                    $q->whereNotNull('sla_resolution_time');
                });

            $query->get()->each(function ($ticket) use (&$query, $request) {
                $actualDuration = SlaHelper::calculateWorkingMinutes($ticket->created_at, $ticket->solved_at);
                $slaLimit = $ticket->user->company->sla_resolution_time;
                $isLate = $actualDuration > $slaLimit;

                if (($request->sla_status === 'on_time' && $isLate) || ($request->sla_status === 'late' && !$isLate)) {
                    $query->where('id', '!=', $ticket->id);
                }
            });
        }

        $tickets = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    public function show($ticket_id)
{
    $normalized = ltrim(preg_replace('/[^0-9]/', '', $ticket_id), '0');

    $ticket = Ticket::with('replies', 'user.company')
    ->where('ticket_id', 'like', "%{$ticket_id}%")
    ->firstOrFail();


    if (!$ticket) {
        return response()->json([
            'success' => false,
            'message' => 'Tiket tidak ditemukan.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $ticket
    ]);
}


    public function updateStatus(Request $request, $ticket_id)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);

        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        $ticket->status = $validated['status'];
        $ticket->status_changed_at = now();

        if ($ticket->status === 'in_progress' && is_null($ticket->response_at)) {
            $ticket->response_at = now();
        }

        if ($ticket->status === 'closed' && is_null($ticket->solved_at)) {
            $ticket->solved_at = now();
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Status tiket berhasil diperbarui.',
            'data' => $ticket
        ]);
    }
}
