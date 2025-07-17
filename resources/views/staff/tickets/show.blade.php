<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            🎫Detail Tiket
        </h2>
    </x-slot>

    <div class="container">
        <div class="card">
            <h3 class="card-title">Judul: {{ $ticket->subject }}</h3>
            <p>ID Tiket: <strong>{{ $ticket->ticket_id }}</strong></p>
            <p>Perusahaan: <strong>{{ $ticket->user->company->name ?? '-' }}</strong></p>
            <p>Dibuat oleh: <strong>{{ $ticket->user->name }}</strong></p>
            <p>Pesan: {{ $ticket->message }}</p>
            <p>Status: <strong>{{ ucfirst($ticket->status) }}</strong></p>
            {{-- Informasi SLA Lengkap --}}
@if ($ticket->actual_response_time !== null)
    <p>
        ⏱️ <strong>Durasi Respon:</strong> {{ round($ticket->actual_response_time, 2) }} menit
    </p>
    <p>
        🎯 <strong>Batas SLA:</strong> {{ $ticket->user->company->sla_response_time ?? '-' }} menit
    </p>
    <p>
        📊 <strong>Evaluasi SLA Response:</strong>
        <span class="{{ $ticket->sla_response_status === 'Terlambat' ? 'text-red-600' : 'text-green-600' }}">
            {{ $ticket->sla_response_status }}
        </span>
    </p>
@else
    <p>📊 <strong>Evaluasi SLA Response:</strong> Belum direspons</p>
@endif

<p>
    📊 <strong>Evaluasi SLA Penyelesaian:</strong>
    <span class="{{ $ticket->sla_resolution_status === 'Terlambat' ? 'text-red-600' : 'text-green-600' }}">
        {{ $ticket->sla_resolution_status }}
    </span>
</p>

@if ($ticket->solved_at)
    <p>
        ✅ <strong>Diselesaikan pada:</strong> {{ $ticket->solved_at->format('d M Y H:i') }}
    </p>
@endif


            @if($ticket->status_changed_at)
                <p class="small-text">Terakhir diubah pada {{ $ticket->status_changed_at->format('d M Y H:i') }}</p>
            @endif
            <p class="small-text">Dibuat pada: {{ $ticket->created_at->format('d M Y H:i') }}</p>

            @if ($ticket->attachment)
                <img src="{{ asset('storage/' . $ticket->attachment) }}" alt="Lampiran Tiket" class="attachment-image">
            @else
                <p class="text-muted">Tidak ada attachment</p>
            @endif

            @if(auth()->user()->hasRole(['admin', 'staff']))
                <form action="{{ route('staff.tickets.updateStatus', $ticket->ticket_id) }}" method="POST" class="status-form">
                    @csrf
                    @method('PATCH')

                    <label for="status">Ubah Status Tiket</label>
                    <select name="status" id="status">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>

                    <button type="submit" class="btn-green">✅ Simpan Perubahan Status</button>
                </form>
            @endif
        </div>

        {{-- Riwayat Balasan --}}
        <div class="card">
            <h3 class="card-title">💬 Riwayat Balasan</h3>
            @forelse ($ticket->replies as $reply)
                @if ($reply->visibility === 'public' || auth()->user()->hasAnyRole(['admin', 'staff']))
                    <div class="reply">
                        <p><strong>{{ $reply->user->name }}</strong>
                            <span class="tag {{ $reply->visibility }}">{{ $reply->visibility }}</span>
                            • <span class="small-text">{{ $reply->created_at->diffForHumans() }}</span>
                        </p>
                        <p>{{ $reply->message }}</p>
                        @if ($reply->attachment)
                            <img src="{{ asset('storage/' . $reply->attachment) }}" alt="Lampiran Balasan" class="attachment-image">
                        @endif
                    </div>
                @endif
            @empty
                <p class="text-muted">Belum ada balasan.</p>
            @endforelse
        </div>

        {{-- Form Balasan --}}
        <div class="card">
            <h3 class="card-title">✉️ Kirim Balasan</h3>

            <form method="POST" action="{{ route('tickets.reply', $ticket->ticket_id) }}" enctype="multipart/form-data">
                @csrf

                <label for="message">Pesan</label>
                <textarea name="message" id="message" rows="4">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-error">{{ $message }}</p>
                @enderror

                @if(auth()->user()->hasAnyRole(['admin', 'staff']))
                    <label for="visibility">Visibilitas</label>
                    <select name="visibility" id="visibility">
                        <option value="public">Publik (dilihat user)</option>
                        <option value="internal">Internal (hanya admin dan support)</option>
                    </select>
                @else
                    <input type="hidden" name="visibility" value="public">
                @endif

                <label for="attachment">Lampiran (opsional)</label>
                <input type="file" name="attachment" id="attachment" accept="image/*" onchange="previewImage(event)">
                @error('attachment')
                    <p class="text-error">{{ $message }}</p>
                @enderror

                <div id="preview-container" style="display: none;">
                    <img id="preview-image" src="#" alt="Preview Gambar" class="attachment-image">
                    <button type="button" class="btn-red small" onclick="removeImage()">❌ Hapus Gambar</button>
                </div>

                <button type="submit" class="btn-blue">🚀 Kirim Balasan</button>
            </form>

            <a href="{{ route('admin.tickets.index') }}" class="btn-dark">← Kembali</a>
        </div>
    </div>

    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            background-color: #2563eb;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .status-form {
            margin-top: 1rem;
        }

        label {
            display: block;
            margin-top: 1rem;
            font-weight: 600;
        }

        input[type="file"],
        select,
        textarea {
            width: 100%;
            margin-top: 0.5rem;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }

        .btn-green, .btn-blue, .btn-dark, .btn-red {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            font-weight: bold;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
        }

        .btn-green {
            background-color: #16a34a;
            color: white;
        }

        .btn-blue {
            background-color: #2563eb;
            color: white;
        }

        .btn-dark {
            background-color: #1f2937;
            color: white;
            border: 2px solid #ef4444;
            text-decoration: none;
        }

        .btn-red {
            background-color: #dc2626;
            color: white;
            margin-top: 0.5rem;
            padding: 0.25rem 0.75rem;
        }

        .btn-red.small {
            font-size: 0.85rem;
        }

        .attachment-image {
            margin-top: 1rem;
            max-width: 200px;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
        }

        .text-muted {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .text-error {
            color: red;
            font-size: 0.875rem;
        }

        .reply {
            border-bottom: 2px dashed #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .tag {
            font-size: 0.85rem;
            padding: 0.15rem 0.5rem;
            border-radius: 0.375rem;
            margin-left: 0.5rem;
        }

        .tag.public {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .tag.internal {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .small-text {
            font-size: 0.85rem;
            color: #6b7280;
        }
    </style>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                previewImage.src = '#';
            }
        }

        function removeImage() {
            const fileInput = document.getElementById('attachment');
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');

            fileInput.value = '';
            previewImage.src = '#';
            previewContainer.style.display = 'none';
        }
    </script>
</x-app-layout>
