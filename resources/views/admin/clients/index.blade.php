<x-app-layout>
    <style>
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 40px 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            color: #f9fafb;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }

        .btn-orange {
            background-color: #f59e0b;
        }

        .btn-indigo {
            background-color: #6366f1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            color: #e5e7eb;
        }

        thead {
            background-color: #374151;
        }

        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 10px;
            border-top: 1px solid #475569;
        }

        tbody tr:hover {
            background-color: #1e293b;
        }

        .btn-action {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #3b82f6;
            color: white;
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead {
                display: none;
            }
            tbody tr {
                background-color: #1f2937;
                margin-bottom: 10px;
                border-radius: 6px;
                overflow: hidden;
            }
            td {
                display: flex;
                justify-content: space-between;
                padding: 8px 12px;
                border-top: none;
                border-bottom: 1px solid #374151;
            }
            td:last-child {
                border-bottom: none;
            }
            td::before {
                content: attr(data-label);
                font-weight: 500;
                color: #9ca3af;
            }
        }
    </style>

    <div class="container">
        <div class="header">
            <h1 class="title">Daftar Client</h1>
            <div class="action-buttons">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-orange">🏢 Daftar Perusahaan</a>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-indigo">+ Tambah User</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Role</th>
                    <th>Perusahaan</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $index => $user)
                    <tr>
                        <td data-label="#">{{ $index + 1 }}</td>
                        <td data-label="Nama">{{ $user->name }}</td>
                        <td data-label="Email">{{ $user->email }}</td>
                        <td data-label="Telepon">{{ $user->phone ?? '-' }}</td>
                        <td data-label="Role">{{ $user->getRoleNames()->first() ?? '-' }}</td>
                        <td data-label="Perusahaan">{{ $user->company->name ?? '-' }}</td>
                        <td data-label="Aksi" style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 6px;">
                                <a href="{{ route('admin.clients.edit', $user->id) }}" class="btn-action btn-edit">✏️ Edit</a>
                                <form action="{{ route('admin.clients.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
