<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Events\TicketStatusUpdatedTelegram;
use App\Helpers\WhatsappHelper;
use App\Http\Controllers\WAInboxController;



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

        return view('staff.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('user.company');
        $company = $ticket->user->company;

        // === SLA Resolution
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

        // === SLA Response
        $responseMinutes = $company->sla_response_time ?? null;

        if ($responseMinutes && $ticket->response_at) {
            $actualResponseTime = $ticket->created_at->diffInMinutes($ticket->response_at);
            $ticket->sla_response_status = $actualResponseTime <= $responseMinutes ? 'Tepat Waktu' : 'Terlambat';
            $ticket->actual_response_time = $actualResponseTime;
        } else {
            $ticket->sla_response_status = 'Belum direspons';
            $ticket->actual_response_time = null;
        }

        return view('staff.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,closed']);

        if ($ticket->status !== $request->status) {
            $ticket->status = $request->status;
            $ticket->status_changed_at = now();

            if ($ticket->status === 'in_progress' && is_null($ticket->response_at)) {
                $ticket->response_at = now();
            }

            if ($ticket->status === 'closed' && is_null($ticket->solved_at)) {
                $ticket->solved_at = now();

                app(WAInboxController::class)->kirimSurveyJikaTiketSelesai($ticket);
            }

            $statusMessage = match ($ticket->status) {
        'in_progress' => "🔧 Tiket #{$ticket->ticket_id} sedang kami proses.",
        'closed' => "✅ Tiket #{$ticket->ticket_id} telah selesai. Terima kasih atas kesabarannya.",
        default => "ℹ️ Status tiket #{$ticket->ticket_id} diperbarui menjadi {$ticket->status}.",
    };

    event(new TicketStatusUpdatedTelegram($ticket, $statusMessage));
    WhatsappHelper::send($ticket->user->phone, $statusMessage);

            $ticket->save();
        }

        return redirect()->route('staff.tickets.show', $ticket)
            ->with('success', 'Status telah diperbarui.');
    }

    public function openAndRedirect(Ticket $ticket)
    {
        $sendNotif = false;

if ($ticket->status === 'open') {
    $ticket->status = 'in_progress';
    $ticket->status_changed_at = now();
    $sendNotif = true;

    if (is_null($ticket->response_at)) {
        $ticket->response_at = now();
    }
}

if ($ticket->status === 'closed' && is_null($ticket->solved_at)) {
    $ticket->solved_at = now();
    $sendNotif = true;

    app(WAInboxController::class)->kirimSurveyJikaTiketSelesai($ticket);
}

if ($sendNotif) {
    $statusMessage = match ($ticket->status) {
        'in_progress' => "🔧 Tiket #{$ticket->ticket_id} sedang kami proses.",
        'closed' => "✅ Tiket #{$ticket->ticket_id} telah selesai. Terima kasih atas kesabarannya.",
        default => "ℹ️ Status tiket #{$ticket->ticket_id} diperbarui menjadi {$ticket->status}.",
    };

    event(new TicketStatusUpdatedTelegram($ticket, $statusMessage));
    WhatsappHelper::send($ticket->user->phone, $statusMessage);
}


        $ticket->save();

        return redirect()->route('staff.tickets.show', ['ticket' => $ticket->ticket_id]);
    }
}
