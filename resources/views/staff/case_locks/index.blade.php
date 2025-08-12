<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title flex items-center gap-2">
            📂 {{ __('Case Log') }}
        </h2>
    </x-slot>

    {{-- Search (center top) --}}
    <div class="search-row">
        <form method="GET" action="{{ route('staff.case_locks.index') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Cari tanggal atau judul...">
            <button type="submit" class="btn btn-info">🔍 Cari</button>
            @if(request('search'))
                <a href="{{ route('staff.case_locks.index') }}" class="btn btn-warning">❌ Reset</a>
            @endif
        </form>
    </div>

    {{-- Controls row: statistik (left) + tambah (right) --}}
    <div class="controls-row">
        <div class="stat-card">
            <div class="stat-number">{{ $cases->count() }}</div>
            <div class="stat-label">Total Case</div>
        </div>

        <div class="add-button-wrapper">
            <a href="{{ route('staff.case_locks.create') }}" class="btn btn-primary add-btn">
                ➕ Tambah Case Log
            </a>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Teknisi</th>
                            <th>Judul</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $case)
                            <tr>
                                <td data-label="Tanggal">{{ \Carbon\Carbon::parse($case->date)->format('d M Y H:i') }}</td>
                                <td data-label="Teknisi">{{ $case->technician_name }}</td>
                                <td data-label="Judul">{{ $case->title }}</td>
                                <td class="table-actions" data-label="Aksi">
                                    <a href="{{ route('staff.case_locks.show', $case) }}" class="btn btn-info">Lihat</a>
                                    <a href="{{ route('staff.case_locks.edit', $case) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('staff.case_locks.destroy', $case) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $cases->links() }}
            </div>
        </div>
    </div>

    <style>
        /* ===== Container & Title ===== */
        :root{
            --blue-600: #2563eb;
            --blue-400: #3b82f6;
            --info: #0ea5e9;
            --warning: #facc15;
            --danger: #dc2626;
            --text-dark: #374151;
            --muted: #555;
            --card-bg: #ffffff;
            --soft-gray: #f3f4f6;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--blue-600), var(--blue-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 18px;
        }

        /* ===== Search Row (center top) ===== */
        .search-row {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
            padding-top: 4px;
        }
        .search-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-input {
            min-width: 340px;
            max-width: 560px;
            width: 45vw;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            outline: none;
            transition: box-shadow .15s ease, border-color .15s ease;
            background: white;
        }
        .form-input:focus {
            box-shadow: 0 0 0 4px rgba(37,99,235,0.06);
            border-color: var(--blue-600);
        }

        /* ===== Controls Row (below search) =====
           Left: statistik
           Right: tombol tambah (tepat di atas kanan tabel)
        */
        .controls-row {
            max-width: 1100px;
            margin: 0 auto 14px auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--soft-gray), #ffffff);
            border-radius: 8px;
            padding: 14px 18px;
            text-align: left;
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
            display: inline-block;
            min-width: 160px;
        }
        .stat-number {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--blue-600);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.9rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .add-button-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            min-width: 160px;
        }
        .add-btn {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            color: #fff;
            background: var(--blue-600);
            display: inline-block;
            transition: transform .12s ease, box-shadow .12s ease;
            box-shadow: 0 3px 8px rgba(37,99,235,0.12);
        }
        .add-btn:hover { transform: translateY(-2px); }

        /* ===== Buttons (general) ===== */
        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            transition: all 0.16s ease;
            display: inline-block;
            border: none;
            background: var(--blue-600);
        }
        .btn-info { background-color: var(--info); color: #fff; }
        .btn-warning { background-color: var(--warning); color: #111; }
        .btn-danger { background-color: var(--danger); color: #fff; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.08); }

        /* ===== Alert ===== */
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        /* ===== Card & Table ===== */
        .card {
            background: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
            padding: 16px;
            overflow: hidden;
        }
        .table-wrapper { overflow-x: auto; }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 640px;
        }
        .table th {
            background: #f3f4f6;
            color: var(--text-dark);
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #e6e6e6;
            font-weight: 600;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
        }
        .table tbody tr:hover { background: #fafafa; }
        .table-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .empty {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }

        .pagination { margin-top: 14px; }

        /* ===== Responsive ===== */
        @media (max-width: 880px) {
            .form-input { min-width: 260px; width: 60vw; }
            .controls-row { padding: 0 12px; }
            .add-button-wrapper { min-width: 120px; }
        }

        @media (max-width: 640px) {
            /* stack: search -> stat -> add -> table */
            .search-row { margin-bottom: 12px; padding: 0 12px; }
            .search-form { width: 100%; justify-content: center; }
            .form-input { width: 100%; min-width: 0; max-width: none; }

            .controls-row {
                display: block;
                width: 100%;
                padding: 0 12px;
                margin-bottom: 12px;
            }
            .stat-card { width: 100%; margin-bottom: 10px; }
            .add-button-wrapper { width: 100%; display:flex; justify-content:flex-end; margin-bottom: 6px; }

            .table { min-width: 0; }
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tr { background: white; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.04); padding: 10px; }
            .table td {
                border: none;
                padding: 8px 10px;
                position: relative;
                padding-left: 50%;
            }
            .table td:before {
                content: attr(data-label);
                position: absolute;
                left: 12px;
                top: 8px;
                font-weight: 700;
                color: #555;
                white-space: nowrap;
            }
            .table-actions { justify-content: flex-start; gap: 6px; }
        }
    </style>
</x-app-layout>
