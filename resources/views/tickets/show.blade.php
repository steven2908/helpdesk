<x-app-layout>
    <x-slot name="header">
        <h2 class="ticket-header">🎫 Detail Tiket</h2>
    </x-slot>

    <div class="ticket-container">
        <div class="ticket-wrapper">

            {{-- Info Tiket --}}
            <div class="ticket-card">
                <h3 class="ticket-title">📝 Judul: {{ $ticket->subject }}</h3>
                <p>🏢 ID Tiket: <strong>{{ $ticket->ticket_id }}</strong></p>
                <p>🏢 Perusahaan: <strong>{{ $ticket->user->company->name ?? '-' }}</strong></p>
                <p>👤 Dibuat oleh: <strong>{{ $ticket->user->name }}</strong></p>
                <p>💬 Pesan: {{ $ticket->message }}</p>
                <p>📌 Status: <strong>{{ ucfirst($ticket->status) }}</strong></p>
                <p>⚠️ Urgensi: <strong>{{ ucfirst($ticket->urgency) }}</strong></p>
                <p class="ticket-date">🕒 Dibuat pada: {{ $ticket->created_at->format('d M Y H:i') }}</p>
                @if($ticket->status_changed_at)
                    <p class="ticket-date">🔄 Diubah terakhir: {{ $ticket->status_changed_at->format('d M Y H:i') }}</p>
                @endif
                @if ($ticket->attachment)
                    <img src="{{ asset('storage/' . $ticket->attachment) }}" alt="Lampiran Tiket" class="ticket-img">
                @else
                    <p class="ticket-muted">📎 Tidak ada lampiran</p>
                @endif
            </div>

            {{-- Ubah Status --}}
            @if(auth()->user()->hasAnyRole(['admin', 'staff']))
            <div class="ticket-card">
                <form action="{{ route('staff.tickets.updateStatus', $ticket->ticket_id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="ticket-label">Ubah Status Tiket</label>
                    <div class="ticket-flex">
                        <select name="status" id="status" class="ticket-select">
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="btn btn-green">✅ Simpan</button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Riwayat Balasan --}}
            <div class="ticket-card">
                <h3 class="ticket-title">💬 Riwayat Balasan</h3>
                @forelse ($ticket->replies as $reply)
                    @if(auth()->user()->hasRole('user') && $reply->visibility !== 'public')
                        @continue
                    @endif
                    <div class="ticket-reply">
                        <p class="ticket-author">
                            {{ $reply->user->name }}
                            @if($reply->visibility === 'internal')
                                <span class="tag-internal">(internal)</span>
                            @else
                                <span class="tag-public">(public)</span>
                            @endif
                            • <span class="ticket-time">{{ $reply->created_at->diffForHumans() }}</span>
                        </p>
                        <p>{{ $reply->message }}</p>
                        @if ($reply->attachment)
                            <img src="{{ asset('storage/' . $reply->attachment) }}" alt="Lampiran Balasan" class="ticket-img">
                        @endif
                    </div>
                @empty
                    <p class="ticket-muted">Belum ada balasan.</p>
                @endforelse
            </div>

            {{-- Form Balasan --}}
            @if(auth()->user()->hasAnyRole(['admin', 'staff', 'user']))
            <div class="ticket-card">
                <h3 class="ticket-title">✉️ Kirim Balasan</h3>
                <form method="POST" action="{{ route('tickets.reply', $ticket->ticket_id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="message" class="ticket-label">Pesan</label>
                        <textarea name="message" id="message" rows="4" class="ticket-textarea">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="ticket-error">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(auth()->user()->hasAnyRole(['admin', 'staff']))
                        <div class="form-group">
                            <label for="visibility" class="ticket-label">Visibilitas</label>
                            <select name="visibility" id="visibility" class="ticket-select">
                                <option value="public">Publik (dilihat user)</option>
                                <option value="internal">Internal (hanya admin/staff)</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="visibility" value="public">
                    @endif

                    <div class="form-group">
                        <label for="attachment" class="ticket-label">Lampiran (opsional)</label>
                        <input type="file" name="attachment" id="attachment" accept="image/*" class="ticket-file">
                        @error('attachment')
                            <p class="ticket-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-blue">🚀 Kirim Balasan</button>
                </form>

                @if(auth()->user()->hasRole('user'))
    <a href="{{ route('tickets.mine') }}" class="btn btn-dark">← Kembali</a>
@elseif(auth()->user()->hasRole('admin'))
    <a href="{{ route('admin.tickets.index') }}" class="btn btn-dark">← Kembali</a>
@elseif(auth()->user()->hasRole('staff'))
    <a href="{{ route('staff.tickets.index') }}" class="btn btn-dark">← Kembali</a>
@endif

            </div>
            @endif

        </div>
    </div>

    {{-- Tambahkan CSS di bawah --}}
    <style>
    .ticket-header {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        border-bottom: 2px solid white;
        padding: 0.5rem 1rem;
        background-color: #2563eb;
        border-radius: 0.5rem;
        text-align: center;
    }

    .ticket-container {
        padding-top: 1.5rem;
    }

    .ticket-wrapper {
        max-width: 64rem;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .ticket-card {
        background-color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .ticket-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .ticket-date, .ticket-muted {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .ticket-img {
        margin-top: 0.75rem;
        max-width: 100%;
        height: auto;
        border: 1px solid #ccc;
        border-radius: 0.5rem;
    }

    .ticket-flex {
        display: flex;
        flex-direction: row;
        gap: 1rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }

    .ticket-select, .ticket-textarea, .ticket-file {
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 0.375rem;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .ticket-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .ticket-error {
        color: red;
        font-size: 0.875rem;
    }

    .ticket-reply {
        margin-bottom: 2rem;
        border-bottom: 2px dashed #ccc;
        padding-bottom: 1rem;
    }

    .ticket-author {
        font-weight: bold;
        color: #1f2937;
    }

    .ticket-time {
        font-weight: normal;
    }

    .tag-internal {
        font-size: 0.875rem;
        color: #dc2626;
    }

    .tag-public {
        font-size: 0.875rem;
        color: #16a34a;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: bold;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-green {
        background-color: green;
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
    }

    .btn-back-container {
        padding-top: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ticket-wrapper {
            padding: 0 0.75rem;
        }

        .ticket-title {
            font-size: 1rem;
        }

        .ticket-header {
            font-size: 1.25rem;
            padding: 0.75rem;
        }

        .ticket-flex {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            text-align: center;
        }

        .ticket-select,
        .ticket-textarea,
        .ticket-file {
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .ticket-img {
            max-width: 100%;
        }
    }
</style>

</x-app-layout>
