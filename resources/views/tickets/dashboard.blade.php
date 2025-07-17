<style>
    /* Layout responsif ringkasan */
    .ticket-summary {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .ticket-box {
        flex: 1 1 calc(25% - 1rem);
        background: #6b7280;
        color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        text-align: center;
    }

    .ticket-box.open { background: #3b82f6; }
    .ticket-box.in-progress { background: #facc15; color: black; }
    .ticket-box.closed { background: #22c55e; }

    /* Responsive: ubah jadi 100% di HP */
    @media (max-width: 768px) {
        .ticket-box {
            flex: 1 1 100%;
        }
    }

    /* Filter form */
    .filter-form input,
    .filter-form button {
        padding: 0.5rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .filter-form input[type="text"],
    .filter-form input[type="date"] {
        color: black;
    }

    .filter-form button {
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
    }

    /* Tabel responsif */
    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        color: white;
        min-width: 600px;
    }

    thead {
        background: #374151;
    }

    th, td {
        padding: 0.75rem;
        border-top: 1px solid #4b5563;
        text-align: left;
    }

    td[colspan="5"] {
        text-align: center;
    }

    tbody {
    background-color: #1f2937; /* hitam kebiruan, biar kontras dengan teks putih */
}

</style>

<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.5rem; font-weight: bold; color: white;">Dashboard Tiket</h2>
    </x-slot>

    <div style="padding: 2rem; color: white;">
        {{-- Ringkasan Status Tiket --}}
        <div class="ticket-summary">
            <a href="{{ route('dashboard') }}" class="ticket-box">
                <strong>Semua Tiket</strong>
                <p>{{ $countOpen + $countInProgress + $countClosed }}</p>
            </a>
            <a href="{{ route('dashboard', ['status' => 'open']) }}" class="ticket-box open">
                <strong>Open</strong>
                <p>{{ $countOpen }}</p>
            </a>
            <a href="{{ route('dashboard', ['status' => 'in_progress']) }}" class="ticket-box in-progress">
                <strong>In Progress</strong>
                <p>{{ $countInProgress }}</p>
            </a>
            <a href="{{ route('dashboard', ['status' => 'closed']) }}" class="ticket-box closed">
                <strong>Closed</strong>
                <p>{{ $countClosed }}</p>
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
            <input type="text" name="search" placeholder="Cari tiket..." value="{{ request('search') }}">
            <input type="date" name="start_date" value="{{ request('start_date') }}">
            <span style="margin: 0 0.5rem;">to</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
            <button type="submit">Filter</button>
        </form>

        {{-- Tabel Tiket Terbaru --}}
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Id Ticket</th>
                        <th>Instansi</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Update Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->ticket_id }}</td>
                            <td>{{ $ticket->user->company->name ?? '-' }}</td>
                            <td>{{ $ticket->user->name }}</td>
                            <td>{{ ucfirst($ticket->status) }}</td>
                            <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Tidak ada tiket ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
