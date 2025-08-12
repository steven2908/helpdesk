<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Daftar Case Log</h2>
    </x-slot>

    <style>
        body {
            background: #f5f7fa;
            font-family: Arial, sans-serif;
        }
        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
        }
        .alert-success {
            background: #eafaf1;
            border-left: 5px solid #27ae60;
            color: #2d6a4f;
            padding: 12px 18px;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        thead {
            background: linear-gradient(90deg, #2980b9, #6dd5fa);
            color: white;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
        }
        th {
            font-weight: 600;
        }
        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }
        tbody tr:last-child {
            border-bottom: none;
        }
        tr:hover {
            background: #f8fbff;
            transition: background 0.2s ease;
        }
        .btn-view {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 14px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-view:hover {
            background: #217dbb;
        }
        /* Pagination */
        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: white;
            color: #555;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .pagination .active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        .pagination a:hover {
            background: #f1f1f1;
        }
        @media (max-width: 600px) {
            th, td {
                padding: 10px;
                font-size: 13px;
            }
            .btn-view {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>

    <div class="container">
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Teknisi</th>
                        <th>Judul</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cases as $case)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($case->date)->format('d-m-Y') }}</td>
                            <td>{{ $case->technician_name }}</td>
                            <td>{{ Str::limit($case->title, 40, '...') }}</td>
                            <td>
                                <form action="{{ route('admin.case_locks.show', $case) }}" method="GET" style="display:inline;">
                                    <button type="submit" class="btn-view">Lihat</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:#888;">
                                Tidak ada data Case Log
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $cases->links('pagination::simple-default') }}
        </div>
    </div>
</x-app-layout>
