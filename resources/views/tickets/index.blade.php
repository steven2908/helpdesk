<x-app-layout>
    <x-slot name="header">
        <h2 class="page-header">Tanggapan Tiket</h2>
    </x-slot>

    <div class="section-container">
        <div class="section-inner">

            <!-- Filter Form -->
            <form method="GET" action="{{ route('tickets.mine') }}" class="filter-form">
                <div class="filter-field">
                    <label for="ticket_id" class="form-label">ID Tiket</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-input">
                </div>

                <div class="filter-field">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-input">
                        <option value="">Semua</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="from_date" class="form-label">Dari Tanggal</label>
                    <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-input">
                </div>

                <div class="filter-field">
                    <label for="to_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-input">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <!-- Ticket Grid -->
            <div class="ticket-grid">
                @forelse ($tickets as $ticket)
                    <div class="ticket-card">
                        <h3 class="ticket-title">
                            ID: {{ $ticket->ticket_id }} &mdash; {{ $ticket->subject }}
                        </h3>

                        <span class="badge"
                              style="background-color: {{
                                  match($ticket->status) {
                                      'open' => '#D1FAE5',
                                      'in_progress' => '#FEF3C7',
                                      'closed' => '#E5E7EB',
                                      default => '#F3F4F6',
                                  }
                              }}; color: {{
                                  match($ticket->status) {
                                      'open' => '#065F46',
                                      'in_progress' => '#92400E',
                                      'closed' => '#374151',
                                      default => '#4B5563',
                                  }
                              }};">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }} &bull;
                            <span class="badge-urgency">
                                Urgency: {{ ucfirst($ticket->urgency ?? '-') }}
                            </span>
                        </span>

                        <p class="ticket-message">
                            {{ Str::limit($ticket->message, 150) }}
                        </p>

                        <div class="ticket-actions">
                            @hasanyrole('admin|staff')
                                <a href="{{ route('admin.tickets.show', $ticket->ticket_id) }}" class="link">Lihat Detail →</a>
                            @else
                                <a href="{{ route('tickets.show', $ticket->ticket_id) }}" class="link">Lihat Detail →</a>
                            @endhasanyrole

                            @hasanyrole('admin|user')
                                <form action="{{ route('tickets.destroy', $ticket->ticket_id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus tiket ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            @endhasanyrole
                        </div>
                    </div>
                @empty
                    <div class="ticket-empty">Tidak ada tiket ditemukan.</div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $tickets->withQueryString()->links() }}
            </div>

        </div>
    </div>

    <style>
        .page-header {
            font-weight: 600;
            font-size: 1.25rem;
            color: white;
            line-height: 1.5;
        }

        .section-container {
            padding: 1.5rem 0;
        }

        .section-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .filter-form {
            margin-bottom: 2rem;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-field {
            flex: 1;
            min-width: 200px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #4B5563;
        }

        .form-input {
            margin-top: 0.25rem;
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #4F46E5;
            color: white;
            text-transform: uppercase;
        }

        .btn-danger {
            background-color: #DC2626;
            color: white;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }

        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .ticket-card {
            background-color: white;
            padding: 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.3s ease;
        }

        .ticket-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 0.5rem;
        }

        .badge {
            display: inline-block;
            margin-bottom: 0.5rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }

        .badge-urgency {
            font-weight: normal;
            color: #6B7280;
        }

        .ticket-message {
            font-size: 0.875rem;
            color: #6B7280;
            margin-bottom: 1rem;
        }

        .ticket-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .link {
            color: #4F46E5;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
        }

        .ticket-empty {
            grid-column: 1 / -1;
            text-align: center;
            color: white;
            padding: 2.5rem 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2.5rem;
        }
    </style>
</x-app-layout>
