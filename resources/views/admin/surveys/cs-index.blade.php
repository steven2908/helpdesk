<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">📊 Hasil Survei Pelayanan CS</h2>
    </x-slot>

    <div class="container">
        <div class="card">
            <div class="table-container">
                <table class="survey-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Pelayanan CS</th>
                            <th>Kualitas Layanan</th>
                            <th>Kepuasan</th>
                            <th>Saran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($surveys as $survey)
                            <tr>
                                <td>
                                    @if ($survey->user)
                                        {{ $survey->user->name }}
                                    @elseif (!empty($survey->nomor_pengirim))
                                        {{ $survey->nomor_pengirim }}
                                    @else
                                        Tidak diketahui
                                    @endif
                                </td>
                                <td><x-badge- :value="$survey->cs_q1" /></td>
                                <td><x-badge- :value="$survey->cs_q2" /></td>
                                <td><x-badge- :value="$survey->cs_q3" /></td>
                                <td>{{ $survey->saran ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">Tidak ada data survei CS.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
        }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        .survey-table {
            width: 100%;
            border-collapse: collapse;
        }

        .survey-table thead {
            background-color: #f2f2f2;
        }

        .survey-table th,
        .survey-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .survey-table td small {
            font-size: 12px;
            color: #555;
        }

        .empty {
            text-align: center;
            color: #888;
            padding: 20px;
        }
    </style>
</x-app-layout>
