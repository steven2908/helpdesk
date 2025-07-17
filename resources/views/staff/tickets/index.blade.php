<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.5rem; font-weight: bold; color: black;">🎫 Manage Tickets</h2>
    </x-slot>

    <style>
        .manage-container {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: auto;
            flex-wrap: wrap;
        }

        .sidebar {
            background: #111827;
            color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            width: 260px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            flex: 1 1 100%;
        }

        .content {
            background: #1f2937;
            color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            flex: 1 1 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            overflow-x: auto;
        }

        @media(min-width: 768px) {
            .sidebar {
                flex: 0 0 260px;
            }

            .content {
                flex: 1;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            min-width: 700px;
        }

        thead {
            background-color: #374151;
        }

        th, td {
            padding: 1rem;
            text-align: center;
            border-top: 1px solid #4b5563;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        tr:hover {
            background-color: #2d3748;
        }

        .urgency-tag {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-weight: bold;
            color: black;
            display: inline-block;
        }

        .filter-btn, .search-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: bold;
            cursor: pointer;
            color: white;
        }

        .filter-btn {
            width: 100%;
            background: #10b981;
        }

        .filter-btn:hover {
            background: #059669;
        }

        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .search-bar input[type="text"] {
            flex: 1;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: 1px solid #9ca3af;
            background-color: #111827;
            color: white;
        }

        .search-btn {
            background: #10b981;
        }

        .search-btn:hover {
            background: #059669;
        }

        .action-link {
            margin-left: 0.75rem;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-block;
        }

        .action-link:hover {
            background-color: #2563eb;
        }

        .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        gap: 4px;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination li {
        display: inline;
    }

    .pagination li a,
    .pagination li span {
        padding: 6px 12px;
        font-size: 0.875rem;
        background-color: #374151;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .pagination li a:hover {
        background-color: #4b5563;
    }

    .pagination li.active span {
        background-color: #10b981;
        color: white;
        font-weight: bold;
    }

    .pagination li.disabled span {
        color: #9ca3af;
        background-color: #1f2937;
    }
    </style>

    <div class="manage-container">
        {{-- Sidebar Filter --}}
        <div class="sidebar">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid #374151; padding-bottom: 0.75rem;">
                🔍 Filter Tiket
            </h3>

            <form method="GET" action="{{ route('staff.tickets.index') }}">
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.95rem;">
                    @foreach([
                        '' => '📋 Semua Tiket',
                        'Open' => '🟢 Open',
                        'In_Progress' => '🟡 In Progress',
                        'Closed' => '🔴 Closed'
                    ] as $value => $label)
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="status" value="{{ $value }}" {{ request('status') == $value ? 'checked' : '' }} />
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="filter-btn">✅ Terapkan Filter</button>
            </form>

            <hr style="margin: 2rem 0; border-color: #374151;">

            <div style="font-size: 0.875rem; color: #9ca3af;">
                <p><strong>Tips:</strong> Kamu bisa memfilter tiket berdasarkan status untuk memudahkan pekerjaan support. 😊</p>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="content">
            <form method="GET" action="{{ route('staff.tickets.index') }}" class="search-bar">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔎 Cari berdasarkan ID, Username, Email" />
                @foreach(request()->except('search') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="search-btn">🔍 Cari</button>
            </form>

            @if ($tickets->count())
                <table>
                    <thead>
                        <tr>
                            <th>ID Tiket</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>🔥 Urgency</th>
                            <th>📂 Status</th>
                            <th>⚙️ Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_id }}</td>
                                <td>{{ $ticket->user->name ?? '-' }}</td>
                                <td>{{ $ticket->user->email ?? '-' }}</td>
                                <td title="{{ $ticket->subject }}">{{ $ticket->subject }}</td>
                                <td>
                                    <span class="urgency-tag" style="background-color:
                                        {{ $ticket->urgency === 'low' ? '#4ade80' :
                                           ($ticket->urgency === 'medium' ? '#facc15' :
                                           ($ticket->urgency === 'high' ? '#f97316' : '#ef4444')) }};">
                                        {{ $ticket->urgency === 'low' ? '🟢 Low' : ($ticket->urgency === 'medium' ? '🟡 Medium' : ($ticket->urgency === 'high' ? '🟠 High' : '🔴 Critical')) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('staff.tickets.updateStatus', $ticket) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.requestSubmit()"
                                            style="padding: 0.5rem; border-radius: 0.375rem; background-color: #111827; color: white; border: 1px solid #4b5563;">
                                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>🟢 Open</option>
                                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>🟡 In Progress</option>
                                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>🔴 Closed</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('staff.tickets.openAndRedirect', $ticket) }}" class="action-link">💬 Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 1.5rem; text-align: center;">
                    {{ $tickets->appends(request()->query())->links() }}
                </div>
            @else
                <p style="color: #d1d5db;">❗ Belum ada tiket yang tersedia.</p>
            @endif
        </div>
    </div>
</x-app-layout>
