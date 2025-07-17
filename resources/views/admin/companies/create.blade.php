<x-app-layout>
    <x-slot name="header">
        <h2 class="page-header">
            🏢 Tambah Perusahaan Baru
        </h2>
    </x-slot>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            max-width: 640px;
            margin: 24px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .form-error {
            font-size: 0.875rem;
            color: #ef4444;
            margin-top: 4px;
        }

        .form-button {
            background-color: #16a34a;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-button:hover {
            background-color: #15803d;
        }

        .page-header {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            background-color: #16a34a;
            padding: 12px 18px;
            border-radius: 8px;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 16px;
                padding: 20px;
                border-radius: 10px;
            }

            .page-header {
                display: block;
                text-align: center;
                font-size: 18px;
                margin: 0 auto;
                padding: 10px;
            }

            .form-button {
                width: 100%;
                margin-top: 16px;
            }
        }
    </style>

    <div class="form-container">
        <form action="{{ route('admin.companies.store') }}" method="POST">
            @csrf

            {{-- Nama Perusahaan --}}
            <div class="form-group">
                <label for="name" class="form-label">Nama Perusahaan</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" class="form-input">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="form-group">
                <label for="address" class="form-label">Alamat</label>
                <textarea name="address" id="address" rows="3" class="form-textarea">{{ old('address') }}</textarea>
                @error('address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- No. Telepon --}}
            <div class="form-group">
                <label for="phone" class="form-label">No. Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-input">
                @error('phone')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div style="text-align: right;">
                <button type="submit" class="form-button">💾 Simpan Perusahaan</button>
            </div>
        </form>
    </div>
</x-app-layout>
