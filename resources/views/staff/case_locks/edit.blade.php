<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: #fff; font-weight: bold;">
            {{ __('Edit Case Log') }}
        </h2>
    </x-slot>

    <style>
        .form-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .form-container.dark {
            background: #1f2937;
        }
        .form-label {
            font-weight: 700;
            font-size: 15px;
            color: #374151; /* default light mode */
            margin-bottom: 6px;
            display: block;
         }
        .dark .form-label {
            color: #f9fafb; /* lebih kontras di dark mode */
        }
        .form-input, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus, .form-textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.2);
            outline: none;
        }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-cancel {
            background: #6b7280;
            color: white;
        }
        .btn-cancel:hover {
            background: #4b5563;
        }
        .btn-submit {
            background: #2563eb;
            color: white;
        }
        .btn-submit:hover {
            background: #1d4ed8;
        }
    </style>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="form-container dark:bg-gray-800">
                <form action="{{ route('staff.case_locks.update', $caseLock) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label text-gray-700 dark:text-gray-300">Tanggal</label>
                        <input type="date" name="date" value="{{ $caseLock->date }}" class="form-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-gray-700 dark:text-gray-300">Nama Teknisi</label>
                        <input type="text" name="technician_name" value="{{ $caseLock->technician_name }}" class="form-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-gray-700 dark:text-gray-300">Judul</label>
                        <input type="text" name="title" value="{{ $caseLock->title }}" class="form-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-gray-700 dark:text-gray-300">Alasan</label>
                        <textarea name="reason" rows="3" class="form-textarea" required>{{ $caseLock->reason }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-gray-700 dark:text-gray-300">Dampak</label>
                        <textarea name="impact" rows="3" class="form-textarea" required>{{ $caseLock->impact }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" class="form-textarea">{{ $caseLock->notes }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('staff.case_locks.index') }}" class="btn btn-cancel mr-2">Batal</a>
                        <button type="submit" class="btn btn-submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
