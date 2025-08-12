<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Tambah Case Log') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="card">
            <form action="{{ route('staff.case_locks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="date">Tanggal</label>
                    <input type="datetime-local" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="technician_name">Nama Teknisi</label>
                    <input type="text" id="technician_name" name="technician_name" required>
                </div>

                <div class="form-group">
                    <label for="title">Judul</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="reason">Alasan</label>
                    <textarea id="reason" name="reason" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="impact">Dampak</label>
                    <textarea id="impact" name="impact" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea id="notes" name="notes" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Gambar (opsional)</label>
                    <input type="file" id="image" name='image' accept="image/*" onchange="previewImage(event)">
                    <div style="margin-top:10px;">
                        <img id="preview" src="" alt="Preview Gambar" style="max-width: 200px; display: none; border: 1px solid #ccc; padding: 5px;">
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <a href="{{ route('staff.case_locks.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
        }

        .page-title {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff; /* teks putih */
            background-color: #007BFF; /* biru kontras */
            padding: 12px 18px;
            border-radius: 6px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }


        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus {
            border-color: #007BFF;
        }

        textarea {
            resize: vertical;
        }

        .form-actions {
            text-align: right;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
        }

        .btn-primary {
            background-color: #007BFF;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            margin-right: 8px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('preview');

            reader. onload = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</x-app-layout>
