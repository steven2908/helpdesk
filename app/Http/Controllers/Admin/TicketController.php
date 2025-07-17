<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Helpers\SlaHelper; // ✅ Tambahkan ini
use App\Events\TicketStatusUpdatedTelegram;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user.company']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', $search)
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan perusahaan
        if ($request->filled('company_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        // ✅ Filter SLA (dinamis)
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

        $tickets = $query->latest()->paginate(10)->withQueryString();

        $companies = Company::select('id', 'name', 'sla_response_time', 'sla_resolution_time')->orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'companies'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('replies', 'user.company');

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Saat admin/staff mengklik "Lihat & Balas", status diubah dan ditandai sudah dilihat.
     */
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
}

$ticket->save();

if ($sendNotif) {
    $statusMessage = match ($ticket->status) {
        'in_progress' => "🔧 Tiket #{$ticket->ticket_id} sedang kami proses.",
        'closed' => "✅ Tiket #{$ticket->ticket_id} telah selesai. Terima kasih atas kesabarannya.",
        default => "ℹ️ Status tiket #{$ticket->ticket_id} diperbarui menjadi {$ticket->status}.",
    };

    event(new TicketStatusUpdatedTelegram($ticket, $statusMessage));
}


\Log::info('📤 Event dispatched', ['ticket_id' => $ticket->ticket_id]);

        $ticket->load('user.company');

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * PATCH: Ubah status tiket (misalnya dari dropdown).
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);

        $ticket->status = $validated['status'];
        $ticket->status_changed_at = now();

        if ($ticket->status === 'in_progress' && is_null($ticket->response_at)) {
            $ticket->response_at = now();
        }

        if ($ticket->status === 'closed' && is_null($ticket->solved_at)) {
            $ticket->solved_at = now();
        }

        $ticket->save();

        $statusMessage = match ($ticket->status) {
    'in_progress' => "🔧 Tiket #{$ticket->ticket_id} sedang kami proses.",
    'closed' => "✅ Tiket #{$ticket->ticket_id} telah selesai. Terima kasih atas kesabarannya.",
    default => "ℹ️ Status tiket #{$ticket->ticket_id} diperbarui menjadi {$ticket->status}.",
};

event(new TicketStatusUpdatedTelegram($ticket, $statusMessage));

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Status tiket berhasil diperbarui.');
    }
}
