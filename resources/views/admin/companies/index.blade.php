<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Daftar Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Tombol Aksi --}}
            <div class="button-bar">
                <a href="{{ route('admin.clients.index') }}" class="btn-secondary">🔙 Kembali ke Client</a>
                <a href="{{ route('admin.companies.create') }}" class="btn-primary">+ Tambah Perusahaan</a>
            </div>

            {{-- Tabel Perusahaan --}}
            <div class="table-responsive">
                <table class="company-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Perusahaan</th>
                            <th>Alamat</th>
                            <th>No. Telepon</th>
                            <th>Email</th>
                            <th>SLA</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $index => $company)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->address ?? '-' }}</td>
                                <td>{{ $company->phone ?? '-' }}</td>
                                <td>{{ $company->email ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.companies.updateSla', $company->id) }}" method="POST" class="text-left">
                                        @csrf
                                        @method('PATCH')

                                        <label class="flex items-center space-x-2 text-sm">
                                            <input type="checkbox" id="use_sla_{{ $company->id }}" name="use_sla" {{ $company->sla_resolution_time ? 'checked' : '' }}>
                                            <span>Gunakan SLA?</span>
                                        </label>

                                        <div id="sla_fields_{{ $company->id }}" class="mt-2" style="{{ $company->sla_resolution_time ? '' : 'display: none;' }}">
                                            <label class="block text-sm mt-2">Response Time (menit)</label>
                                            <input type="number" name="sla_response_time" min="1" value="{{ $company->sla_response_time ?? '' }}"
                                                class="mt-1 p-1 w-full text-sm rounded bg-gray-800 text-white border border-gray-600">

                                            <label class="block text-sm mt-2">Resolution Time (menit)</label>
                                            <input type="number" name="sla_resolution_time" min="1" value="{{ $company->sla_resolution_time ?? '' }}"
                                                class="mt-1 p-1 w-full text-sm rounded bg-gray-800 text-white border border-gray-600">
                                        </div>

                                        <button type="submit" class="mt-2 text-xs text-white bg-indigo-600 hover:bg-indigo-700 px-2 py-1 rounded">Simpan SLA</button>
                                    </form>
                                </td>
                                <td class="actions" style="text-align: center;">
                                    <a href="{{ route('admin.companies.edit', $company->id) }}" class="edit">Edit</a>
                                    <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const checkbox = document.getElementById('use_sla_{{ $company->id }}');
                                    const slaFields = document.getElementById('sla_fields_{{ $company->id }}');
                                    if (checkbox) {
                                        checkbox.addEventListener('change', function () {
                                            slaFields.style.display = this.checked ? 'block' : 'none';
                                        });
                                    }
                                });
                            </script>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #f87171;">Tidak ada perusahaan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- STYLE --}}
            <style>
                body {
                    background-color: #0f172a !important;
                    color: #e5e7eb !important;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
                }

                .button-bar {
                    display: flex;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 12px;
                    margin-bottom: 20px;
                }

                .btn-primary, .btn-secondary {
                    text-decoration: none;
                    padding: 10px 18px;
                    font-weight: bold;
                    border-radius: 8px;
                    display: inline-block;
                    transition: background-color 0.3s ease;
                    white-space: nowrap;
                }

                .btn-primary {
                    background-color: #10b981;
                    color: white !important;
                }

                .btn-primary:hover {
                    background-color: #059669;
                }

                .btn-secondary {
                    background-color: #334155;
                    color: #e2e8f0 !important;
                }

                .btn-secondary:hover {
                    background-color: #475569;
                }

                .table-responsive {
                    width: 100%;
                    overflow-x: auto;
                }

                .company-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
                    min-width: 800px;
                }

                .company-table th {
                    background-color: #4f46e5;
                    color: white;
                    padding: 14px;
                    text-align: left;
                }

                .company-table td {
                    padding: 12px;
                    vertical-align: top;
                }

                .company-table tr:nth-child(even) {
                    background-color: #1e293b;
                }

                .company-table tr:nth-child(odd) {
                    background-color: #334155;
                }

                .company-table tr:hover {
                    background-color: #475569;
                }

                .actions a,
                .actions button {
                    margin-right: 10px;
                    text-decoration: none;
                    font-weight: 500;
                    background: none;
                    border: none;
                    cursor: pointer;
                }

                .actions a.edit {
                    color: #60a5fa;
                }

                .actions button.delete {
                    color: #f87171;
                }

                @media (max-width: 768px) {
                    .button-bar {
                        flex-direction: column;
                        align-items: stretch;
                    }

                    .company-table th,
                    .company-table td {
                        font-size: 13px;
                        padding: 10px;
                    }

                    .btn-primary, .btn-secondary {
                        width: 100%;
                        text-align: center;
                    }

                    .company-table {
                        min-width: 100%;
                    }
                }
            </style>
        </div>
    </div>
</x-app-layout>
