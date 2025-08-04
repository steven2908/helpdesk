<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title; color: black;">📄 Daftar Tiket</h2>
    </x-slot>

    <style>
        .page-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #f9fafb;
            margin-bottom: 1rem;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding-top: 20px;
        }

        .main-content {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-sidebar {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 200px;
            flex-shrink: 0;
        }

        .btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            text-align: center;
        }

        .btn-outline-primary { color: #3b82f6; border-color: #3b82f6; }
        .btn-outline-primary:hover { background: #3b82f6; color: white; }

        .btn-outline-warning { color: #d97706; border-color: #d97706; }
        .btn-outline-warning:hover { background: #d97706; color: white; }

        .btn-outline-success { color: #10b981; border-color: #10b981; }
        .btn-outline-success:hover { background: #10b981; color: white; }

        .btn-outline-secondary { color: #6b7280; border-color: #6b7280; }
        .btn-outline-secondary:hover { background: #6b7280; color: white; }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .card {
            background-color: #1e293b;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            color: #f9fafb;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .form-control {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            text-transform: capitalize;
        }

        .status-badge.open { background: #1d4ed8; color: white; }
        .status-badge.in_progress { background: #f59e0b; color: white; }
        .status-badge.closed { background: #10b981; color: white; }

        .urgency-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .urgency-badge.low { background: #059669; color: white; }
        .urgency-badge.medium { background: #d97706; color: white; }
        .urgency-badge.high { background: #b91c1c; color: white; }
        .urgency-badge.urgent { background: #7c3aed; color: white; }

        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 5px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            background: #1e293b;
            color: #f9fafb;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #334155;
            font-size: 14px;
        }

        .pagination .page-item.active .page-link {
            background: #3b82f6;
            color: white;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }

            .filter-sidebar {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .filter-sidebar .btn {
                flex: 1 1 calc(50% - 10px);
                text-align: center;
            }
        }
    </style>

    <div class="container">
        <div class="main-content">
            {{-- Sidebar --}}
            <div class="filter-sidebar">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">Semua</a>
                <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}" class="btn btn-outline-primary">Open</a>
                <a href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}" class="btn btn-outline-warning">In Progress</a>
                <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}" class="btn btn-outline-success">Closed</a>
            </div>

            <div style="flex: 1;">
                {{-- Form Pencarian --}}
                <form method="GET" action="{{ route('admin.tickets.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari tiket..." value="{{ request('search') }}">
                        @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>

                {{-- Pesan Sukses --}}
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                {{-- Grid Tiket --}}
                <div class="ticket-grid">
                    @forelse ($tickets as $ticket)
                        <div class="card">
                            <h3 style="font-size: 18px; margin-bottom: 8px;">🎫 {{ $ticket->subject }}</h3>
                            <p><strong>Pengirim:</strong> {{ $ticket->user->name }} ({{ $ticket->user->email }})</p>
                            <p><strong>Status:</strong> <span class="status-badge {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></p>
                            <p><strong>Isi:</strong> {{ Str::limit($ticket->message, 80) }}</p>
                            <p>
                                <strong>Urgensi:</strong>
                                <span class="urgency-badge {{ $ticket->urgency }}">
                                    {{
                                        match($ticket->urgency) {
                                            'low' => '🕯️ Low',
                                            'medium' => '🥊 Medium',
                                            'high' => '♨️ High',
                                            'urgent' => '🚨 Urgent',
                                            default => ucfirst($ticket->urgency)
                                        }
                                    }}
                                </span>
                            </p>

                            {{-- Survey --}}
                            <p>
                                <strong>Survey:</strong>
                                @if ($ticket->survey)
                                    ✅ Sudah disurvei – 
                                    <a href="{{ route('admin.surveys.show', $ticket->survey->id) }}" class="btn btn-outline-primary btn-sm">Lihat Survey</a>
                                @else
                                    🕐 Belum disurvei
                                @endif
                            </p>

                            <div style="margin-top: 12px; display: flex; gap: 8px;">
                                <a href="{{ route('admin.tickets.openAndRedirect', $ticket->ticket_id) }}" class="btn btn-outline-secondary btn-sm">Lihat</a>
                                @can('reply ticket')
                                    <a href="{{ route('admin.tickets.openAndRedirect', $ticket->ticket_id) }}" class="btn btn-primary btn-sm">Balas</a>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p>Tidak ada tiket ditemukan.</p>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="pagination">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
