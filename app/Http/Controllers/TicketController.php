<?php

namespace App\Http\Controllers;

use App\Models\{Ticket};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};
use App\Models\{Company, User};
use App\Events\TicketCreated;
use App\Helpers\WhatsappHelper;

class TicketController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    $query = Ticket::with('user.company');

    // Akses: Admin hanya bisa melihat tiket dari perusahaannya, user hanya bisa melihat tiketnya sendiri
    if ($request->routeIs('tickets.mine')) {
        $query->where('user_id', $user->id);
    } else {
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
        $query->where('company_id', $user->company_id);
    }

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter urgency
    if ($request->filled('urgency')) {
        $query->where('urgency', $request->urgency);
    }

    // Filter pencarian umum
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('ticket_id', $search)
              ->orWhere('subject', 'like', '%' . $search . '%')
              ->orWhere('message', 'like', '%' . $search . '%')
              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('name', 'like', '%' . $search . '%')
                     ->orWhere('email', 'like', '%' . $search . '%');
              });
        });
    }

    // Filter tanggal
    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    // Ambil hasil dengan pagination
    $tickets = $query->latest()->paginate(8)->withQueryString();

    return view('tickets.index', compact('tickets'));
}


    public function create()
    {
        $clients = Company::all();
        return view('tickets.create', compact('clients'));
    }

    public function store(Request $request)
{
    
    $user = Auth::user();

    $rules = [
        'subject' => 'required',
        'message' => 'required',
        'urgency' => 'required|in:low,medium,high,urgent',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
    ];

    // Jika admin, harus isi client_id dan sub_client_id
    if ($user->hasRole('admin')) {
        $rules['client_id'] = 'required|exists:companies,id';
        $rules['sub_client_id'] = 'required|exists:users,id';
    }

    $request->validate($rules);

    $path = null;

    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')->store('attachments', 'public');
    }

    $ticket = Ticket::create([
        'subject' => $request->subject,
        'message' => $request->message,
        'urgency' => $request->urgency,
        'status' => 'open',
        'user_id' => $user->hasRole('admin') ? $request->sub_client_id : $user->id,
        'company_id' => $user->hasRole('admin') ? $request->client_id : $user->company_id,
        'attachment' => $path,
    ]);

    event(new TicketCreated($ticket)); // broadcast ke frontend
    event(new \App\Events\TicketCreatedTelegram($ticket));


    // Kirim notifikasi ke pemilik tiket
$phone = $ticket->user->phone;
$message = "✅ Halo {$ticket->user->name}, tiket Anda berhasil dibuat!\n\n🆔 ID: {$ticket->ticket_id}\n📌 Subjek: {$ticket->subject}\n📍 Status: Open\n\nKami akan segera memprosesnya. Terima kasih 🙏";
WhatsappHelper::send($phone, $message);

// Kirim notifikasi ke semua staff di perusahaan yang sama
$staffs = User::role('staff')
    ->whereNotNull('phone')
    ->get();

foreach ($staffs as $staff) {
    $staffMessage = "📩 Hai {$staff->name},\n\nAda tiket baru yang masuk dari user *{$ticket->user->name}*:\n\n🆔 ID: {$ticket->ticket_id}\n📌 Subjek: {$ticket->subject}\n🎯 Urgensi: {$ticket->urgency}\n\nSilakan dicek di sistem Helpdesk.";
    WhatsappHelper::send($staff->phone, $staffMessage);
}


    \Log::info('✅ Tiket berhasil dibuat dari form web oleh user ID: ' . $user->id);
    return redirect()->route('tickets.mine')->with('success', 'Tiket berhasil dibuat. ID Tiket: #' . $ticket->ticket_id);
}


    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        if (
            $ticket->user_id !== $user->id &&
            !($user->hasRole('admin') && $ticket->company_id === $user->company_id)
        ) {
            abort(403, 'Unauthorized');
        }

        return view('tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);

        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        $ticket->status = $request->status;
        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorizeAdminOnly();
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query();

        if (!$user->hasAnyRole(['admin', 'staff'])) {
    // User hanya boleh lihat tiket miliknya sendiri
    $query->where('user_id', $user->id);
}


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', $search)
                  ->orWhere('subject', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $tickets = $query->latest()->with('user.company')->get();

        $countOpen = (clone $query)->where('status', 'open')->count();
        $countInProgress = (clone $query)->where('status', 'in_progress')->count();
        $countClosed = (clone $query)->where('status', 'closed')->count();

        return view('tickets.dashboard', compact('tickets', 'countOpen', 'countInProgress', 'countClosed'));
    }

    protected function authorizeAdminOnly()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
    }
}
