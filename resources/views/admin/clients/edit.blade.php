<x-app-layout>
    <div class="form-container">
        <h1 class="form-title">✏️ Edit Client</h1>

        <form action="{{ route('admin.clients.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                @error('phone')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Perusahaan --}}
            <div class="form-group">
                <label for="company_id">Perusahaan</label>
                <select name="company_id" required class="form-input">
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ $user->company_id == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" required class="form-input">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ ($userRole == $role) ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="button-group">
                <a href="{{ route('admin.clients.index') }}" class="back-button">🔙 Kembali</a>
                <button type="submit" class="submit-button">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <style>
        body {
            background: linear-gradient(to bottom right, #0f172a, #1e293b);
            color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            max-width: 620px;
            margin: 60px auto;
            padding: 40px 32px;
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .form-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #f8fafc;
            text-align: center;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #cbd5e1;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            font-size: 1rem;
            background-color: #0f172a;
            border: 1px solid #475569;
            border-radius: 10px;
            color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
            background-color: #1e293b;
        }

        .form-error {
            font-size: 0.875rem;
            color: #f87171;
            margin-top: 5px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .back-button {
            display: inline-block;
            text-align: center;
            padding: 12px 24px;
            background-color: #475569;
            color: #f1f5f9;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #64748b;
        }

        .submit-button {
            flex: 1;
            padding: 14px 0;
            background: linear-gradient(to right, #22c55e, #3b82f6);
            color: #ffffff;
            font-weight: 600;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            background: linear-gradient(to right, #3b82f6, #6366f1);
            transform: translateY(-2px);
        }
    </style>
</x-app-layout>
