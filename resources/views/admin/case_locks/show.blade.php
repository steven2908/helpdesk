<x-app-layout>
    <x-slot name="header">
        <h2 style="
            font-family: Arial, sans-serif; 
            font-weight: 700; 
            font-size: 1.8rem; 
            color: #ffffff; 
            background-color: #2563eb; /* biru cerah */
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.5);
            max-width: 700px;
            margin: 2rem auto 1.5rem;
            text-align: center;
        ">
            {{ __('Detail Case Log') }}
        </h2>
    </x-slot>

    <style>
        body {
            background-color: #f3f4f6; /* abu muda */
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Card utama */
        .card {
            max-width: 700px;
            background: #fff;
            margin: 0 auto 3rem;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow:
                0 4px 12px rgb(0 0 0 / 0.1),
                0 2px 8px rgb(0 0 0 / 0.06);
        }

        /* Tombol kembali */
        .back-link {
            display: inline-block;
            margin-bottom: 1.75rem;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
            font-size: 1.05rem;
            border: 2px solid transparent;
            padding: 6px 12px;
            border-radius: 6px;
            transition: 
                color 0.3s ease,
                border-color 0.3s ease,
                background-color 0.3s ease;
        }
        .back-link:hover {
            color: #1e40af;
            border-color: #1e40af;
            background-color: #e0e7ff;
            text-decoration: none;
        }

        /* Label tiap field */
        .label {
            font-weight: 700;
            font-size: 1.1rem;
            color: #4b5563;
            margin-bottom: 0.35rem;
        }

        /* Nilai field */
        .value {
            font-size: 1.2rem;
            color: #111827;
            line-height: 1.4;
            white-space: pre-wrap; /* agar line break tampil kalau ada */
        }

        /* Wrapper tiap field */
        .field {
            margin-bottom: 1.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Responsive */
        @media (max-width: 720px) {
            .card {
                padding: 1.5rem 1.8rem;
                margin: 1rem;
            }
            h2 {
                font-size: 1.5rem !important;
                padding: 0.8rem 1rem !important;
            }
        }
    </style>

    <div class="card">
        <a href="{{ route('admin.case_locks.index') }}" class="back-link">← Kembali ke daftar</a>

        <div class="field">
            <div class="label">Tanggal:</div>
            <div class="value">{{ \Carbon\Carbon::parse($caseLock->date)->format('d-m-Y') }}</div>
        </div>

        <div class="field">
            <div class="label">Teknisi:</div>
            <div class="value">{{ $caseLock->technician_name }}</div>
        </div>

        <div class="field">
            <div class="label">Judul:</div>
            <div class="value">{{ $caseLock->title }}</div>
        </div>

        <div class="field">
            <div class="label">Alasan:</div>
            <div class="value">{{ $caseLock->reason }}</div>
        </div>

        <div class="field">
            <div class="label">Dampak:</div>
            <div class="value">{{ $caseLock->impact }}</div>
        </div>

        <div class="field">
            <div class="label">Catatan:</div>
            <div class="value">{{ $caseLock->notes ?? '-' }}</div>
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
    </div>
</x-app-layout>
