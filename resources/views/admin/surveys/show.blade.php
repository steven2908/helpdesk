<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            📝 Detail Survei Pengguna
        </h2>
    </x-slot>

    <style>
        .survey-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-family: sans-serif;
        }

        .survey-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #3490dc;
            padding-bottom: 5px;
            color: #2d3748;
        }

        .survey-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #f9f9f9;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }

        .badge.green { background-color: #38a169; }
        .badge.yellow { background-color: #f6ad55; }
        .badge.red { background-color: #e53e3e; }

        .saran-box {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid #ccc;
        }

        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 16px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        @media (max-width: 600px) {
            .survey-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .survey-item span:last-child {
                margin-top: 5px;
            }
        }
    </style>

    <div class="survey-container">
        <div class="survey-title">📋 Informasi Survei</div>

        <p><strong>👤 User:</strong> {{ $survey->user ? $survey->user->name : 'Tidak diketahui' }}</p>

        @if($survey->ticket)
            <p><strong>🎫 Tiket ID:</strong> #{{ $survey->ticket->ticket_id }}</p>
            <p><strong>📌 Subject:</strong> {{ $survey->ticket->subject }}</p>
        @endif

        <div style="margin-top: 20px;">
            @if($survey->q1)
                @foreach([
                    '1. Responsivitas Tim' => $survey->q1,
                    '2. Komunikasi & Koordinasi' => $survey->q2,
                    '3. Sikap & Keramahan Tim' => $survey->q3,
                    '4. Pengetahuan Teknis' => $survey->q4,
                    '5. Kepuasan Keseluruhan' => $survey->q5,
                ] as $label => $nilai)
                    <div class="survey-item">
                        <span>{{ $label }}</span>
                        <span class="badge
                            @if($nilai >= 4) green
                            @elseif($nilai == 3) yellow
                            @else red @endif">
                            {{ $nilai }}
                        </span>
                    </div>
                @endforeach
            @elseif($survey->cs_q1)
                @foreach([
                    '1. Pelayanan CS' => $survey->cs_q1,
                    '2. Kualitas Layanan CS' => $survey->cs_q2,
                    '3. Kepuasan terhadap CS' => $survey->cs_q3,
                ] as $label => $nilai)
                    <div class="survey-item">
                        <span>{{ $label }}</span>
                        <span class="badge
                            @if($nilai >= 4) green
                            @elseif($nilai == 3) yellow
                            @else red @endif">
                            {{ $nilai }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>

        @if($survey->saran)
            <div class="saran-box">
                <strong>💬 Saran Tambahan:</strong><br>
                {{ $survey->saran }}
            </div>
        @endif

        <a href="{{ route('admin.tickets.index') }}" class="back-button">⬅️ Kembali ke Tiket</a>
    </div>
</x-app-layout>
