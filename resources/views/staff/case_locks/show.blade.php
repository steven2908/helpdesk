<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title text-white" style="color: #fff; font-weight: bold;">
            {{ __('Detail Case Log') }}
        </h2>
    </x-slot>


    <div class="container">
        <div class="card detail-card">
            <div class="detail-item">
                <span class="label">📅 Tanggal:</span>
                <span class="value">{{ $caseLock->date }}</span>
            </div>
            <div class="detail-item">
                <span class="label">👨‍🔧 Nama Teknisi:</span>
                <span class="value">{{ $caseLock->technician_name }}</span>
            </div>
            <div class="detail-item">
                <span class="label">📝 Judul:</span>
                <span class="value">{{ $caseLock->title }}</span>
            </div>
            <div class="detail-item">
                <span class="label">💡 Alasan:</span>
                <span class="value">{{ $caseLock->reason }}</span>
            </div>
            <div class="detail-item">
                <span class="label">⚠️ Dampak:</span>
                <span class="value">{{ $caseLock->impact }}</span>
            </div>
            <div class="detail-item">
                <span class="label">📌 Catatan:</span>
                <span class="value">{{ $caseLock->notes ?? '-' }}</span>
            </div>

            <div class="detail-item">
                <span class="label">Gambar:</span>
                @if($caseLock->image)
                <img src="{{ asset('storage/' . $caseLock->image) }}"
                alt="Gambar Case Log"
                style="max-width: 300px; border-radius: 6px; border: 1px solid #ccc;">

                @else
                <span class="value">-</span>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('staff.case_locks.index') }}" class="btn btn-secondary">⬅ Kembali</a>
            </div>
        </div>
    </div>

    <style>
        /* Container */
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        /* Title */
        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #222;
            margin-bottom: 20px;
        }

        /* Card */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
        }

        /* Detail item */
        .detail-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        .label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 4px;
        }
        .value {
            color: #4b5563;
            line-height: 1.5;
        }

        /* Actions */
        .actions {
            margin-top: 20px;
            text-align: right;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-secondary {
            background: #6b7280;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .detail-item {
                font-size: 14px;
                padding: 8px 10px;
            }
            .page-title {
                font-size: 1.4rem;
            }
            .btn {
                font-size: 13px;
                padding: 6px 12px;
            }
        }
    </style>
</x-app-layout>
