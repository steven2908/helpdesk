<x-app-layout>
    <style>
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 40px 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            color: #f9fafb;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .action-buttons a {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            color: white;
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
            font-size: 15px;
            color: #e5e7eb;
        }

        thead {
            background-color: #4f46e5;
        }

        th {
            padding: 14px;
            text-align: left;
            border-bottom: 3px solid #312e81;
        }

        td {
            padding: 12px;
        }

        tbody tr:nth-child(even) {
            background-color: #1e293b;
        }

        tbody tr:nth-child(odd) {
            background-color: #334155;
        }

        tbody tr:hover {
            background-color: #475569;
        }

        .action-link {
            color: #93c5fd;
            font-weight: 500;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 15px;
                border: 1px solid #475569;
                border-radius: 8px;
                overflow: hidden;
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 10px 14px;
                border-bottom: 1px solid #475569;
            }

            td:last-child {
                border-bottom: none;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #cbd5e1;
            }
        }
    </style>

    <div class="container">
        <div class="header">
            <h1 class="title">Daftar Client</h1>
            <div class="action-buttons">
                <a href="{{ route('admin.companies.index') }}" class="btn-orange">🏢 Daftar Perusahaan</a>
                <a href="{{ route('admin.clients.create') }}" class="btn-indigo">+ Tambah User</a>
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
                        <td data-label="#"> {{ $index + 1 }} </td>
                        <td data-label="Nama"> {{ $user->name }} </td>
                        <td data-label="Email"> {{ $user->email }} </td>
                        <td data-label="Telepon"> {{ $user->phone ?? '-' }} </td>
                        <td data-label="Role"> {{ $user->getRoleNames()->first() ?? '-' }} </td>
                        <td data-label="Perusahaan"> {{ $user->company->name ?? '-' }} </td>
                        <td data-label="Aksi" style="text-align: center;">
                            <a href="{{ route('admin.clients.edit', $user->id) }}" class="action-link">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
