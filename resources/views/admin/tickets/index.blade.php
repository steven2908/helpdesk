<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title; color: black;">📄 Daftar Tiket</h2>
    </x-slot>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

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

        table.ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            color: #f9fafb;
        }

        table.ticket-table thead {
            background-color: #334155;
        }

        table.ticket-table th,
        table.ticket-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #475569;
            text-align: left;
        }

        table.ticket-table tbody tr:hover {
            background-color: #1f2937;
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

        /* Pagination */
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

        /* Responsive */
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

                {{-- Filter Perusahaan + SLA --}}
                <div style="margin-top: 30px;">
                    <h4 style="font-size: 15px; color: #e2e8f0;">📌 Filter Perusahaan</h4>
                    <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 10px;">
                        @foreach($companies as $company)
                            <a href="{{ route('admin.tickets.index', ['company_id' => $company->id]) }}"
                                class="btn btn-outline-secondary"
                                style="{{ request('company_id') == $company->id ? 'background: #3b82f6; color: white;' : '' }}">
                                {{ $company->name }}
                            </a>
                        @endforeach
                    </div>

                    @if(request('company_id'))
                        @php
                            $selectedCompany = $companies->firstWhere('id', request('company_id'));
                        @endphp

                        @if($selectedCompany && ($selectedCompany->sla_response_time || $selectedCompany->sla_resolution_time))
                            <div style="margin-top: 20px;">
                                <h4 style="font-size: 15px; color: #e2e8f0;">⏱️ SLA</h4>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 10px;">
                                    <a href="{{ route('admin.tickets.index', ['company_id' => request('company_id'), 'sla_status' => 'on_time']) }}"
                                        class="btn btn-outline-success"
                                        style="{{ request('sla_status') === 'on_time' ? 'background: #16a34a; color: white;' : '' }}">
                                        ✅ Tepat Waktu
                                    </a>
                                    <a href="{{ route('admin.tickets.index', ['company_id' => request('company_id'), 'sla_status' => 'late']) }}"
                                        class="btn btn-outline-warning"
                                        style="{{ request('sla_status') === 'late' ? 'background: #f59e0b; color: white;' : '' }}">
                                        ⏰ Terlambat
                                    </a>
                                    <a href="{{ route('admin.tickets.index', ['company_id' => request('company_id')]) }}"
                                        class="btn btn-outline-secondary">❌ Reset SLA</a>
                                </div>
                            </div>
                        @else
                            <p style="margin-top: 10px; font-size: 14px; color: #cbd5e1;">🛑 Tidak ada SLA untuk perusahaan ini.</p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Konten Utama --}}
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

                {{-- Tabel --}}
                <div class="card">
                    <div class="card-body">
                        <table class="ticket-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Pengirim</th>
                                    <th>Status</th>
                                    <th>Isi</th>
                                    <th>Urgency</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>{{ $ticket->user->name }}<br><small>{{ $ticket->user->email }}</small></td>
                                        <td><span class="status-badge {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></td>
                                        <td>{{ Str::limit($ticket->message, 50) }}</td>
                                        <td>
                                            @php
                                                $urgencyClass = $ticket->urgency;
                                                $urgencyLabel = match($ticket->urgency) {
                                                    'low' => '🕯️ Low',
                                                    'medium' => '🥊 Medium',
                                                    'high' => '♨️ High',
                                                    'urgent' => '🚨 Urgent',
                                                    default => ucfirst($ticket->urgency)
                                                };
                                            @endphp
                                            <span class="urgency-badge {{ $urgencyClass }}">{{ $urgencyLabel }}</span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                                <a href="{{ route('admin.tickets.openAndRedirect', $ticket->ticket_id) }}" class="btn btn-outline-secondary btn-sm">Lihat</a>
                                                @can('reply ticket')
                                                    <a href="{{ route('admin.tickets.openAndRedirect', $ticket->ticket_id) }}" class="btn btn-primary btn-sm">Balas</a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">Tidak ada tiket ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="pagination">
                            {{ $tickets->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
