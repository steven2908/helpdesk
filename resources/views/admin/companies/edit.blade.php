<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Edit Perusahaan
        </h2>
    </x-slot>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
        }

        .card {
            background-color: #1e293b;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
            color: #e2e8f0;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #334155;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            background-color: #0f172a;
            color: #f1f5f9;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        input:focus, textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
            outline: none;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }

        .btn-secondary {
            color: #e2e8f0;
            background-color: #334155;
            padding: 0.75rem 1.5rem;
            margin-right: 1rem;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        .alert-success {
            background-color: #16a34a;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .text-red-500 {
            color: #f87171;
        }

        @media (max-width: 640px) {
            .btn-primary, .btn-secondary {
                width: 100%;
                text-align: center;
                margin-bottom: 12px;
            }

            .flex {
                flex-direction: column;
                align-items: stretch;
            }

            .flex.justify-end {
                justify-content: flex-start;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card">

                @if (session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.companies.update', $company) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div>
                        <label>Nama Perusahaan</label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label>Alamat</label>
                        <textarea name="address" rows="3" required>{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label>Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" required>
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required>
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('admin.companies.index') }}" class="btn-secondary">Batal</a>
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
