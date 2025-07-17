<x-app-layout>
    <style>
        body {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .form-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px 30px;
            background-color: #1e293b;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #f8fafc;
        }

        .back-btn {
            background-color: #334155;
            color: #e2e8f0;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .back-btn:hover {
            background-color: #475569;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            background-color: #0f172a;
            color: #f1f5f9;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .error-message {
            font-size: 0.875rem;
            color: #f87171;
            margin-top: 6px;
        }

        .submit-btn {
            background-color: #3b82f6;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }
    </style>

    <div class="form-container">
        <div class="form-header">
            <h1>Tambah Client</h1>
            <a href="{{ route('admin.clients.index') }}" class="back-btn">← Kembali</a>
        </div>

        <form action="{{ route('admin.clients.storeUser') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}">
                @error('phone')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="company_id">Perusahaan</label>
                <select name="company_id" id="company_id" required>
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Simpan</button>
        </form>
    </div>
</x-app-layout>
