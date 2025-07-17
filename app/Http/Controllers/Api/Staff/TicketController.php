<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%$search%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        $tickets = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    public function show($ticket_id)
    {
        $ticket = Ticket::with('user.company')
            ->where('ticket_id', $ticket_id)
            ->firstOrFail();

        $company = $ticket->user->company;

        // SLA Resolution
        $resolutionMinutes = $company->sla_resolution_time ?? 2880;
        $resolutionDeadline = $ticket->created_at->copy()->addMinutes($resolutionMinutes);
        $now = now();

        $ticket->sla_resolution_status = $now->gt($resolutionDeadline) ? 'Terlambat' : 'Tepat Waktu';
        $ticket->remaining_resolution_time = $now->gt($resolutionDeadline)
            ? 'Sudah lewat'
            : $now->diffForHumans($resolutionDeadline, [
                'parts' => 2,
                'short' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            ]);

        // SLA Response
        $responseMinutes = $company->sla_response_time ?? null;

        if ($responseMinutes && $ticket->response_at) {
            $actualResponseTime = $ticket->created_at->diffInMinutes($ticket->response_at);
            $ticket->sla_response_status = $actualResponseTime <= $responseMinutes ? 'Tepat Waktu' : 'Terlambat';
            $ticket->actual_response_time = $actualResponseTime;
        } else {
            $ticket->sla_response_status = 'Belum direspons';
            $ticket->actual_response_time = null;
        }

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    public function updateStatus(Request $request, $ticket_id)
    {
        $request->validate(['status' => 'required|in:open,in_progress,closed']);

        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        if ($ticket->status !== $request->status) {
            $ticket->status = $request->status;
            $ticket->status_changed_at = now();

            if ($ticket->status === 'in_progress' && is_null($ticket->response_at)) {
                $ticket->response_at = now();
            }

            if ($ticket->status === 'closed' && is_null($ticket->solved_at)) {
                $ticket->solved_at = now();
            }

            $ticket->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status telah diperbarui.',
            'data' => $ticket
        ]);
    }

    public function openAndRedirect($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
            $ticket->status_changed_at = now();

            if (is_null($ticket->response_at)) {
                $ticket->response_at = now();
            }
        }

        if ($ticket->status === 'closed' && is_null($ticket->solved_at)) {
            $ticket->solved_at = now();
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Status otomatis diperbarui saat dibuka.',
            'data' => $ticket
        ]);
    }
}
