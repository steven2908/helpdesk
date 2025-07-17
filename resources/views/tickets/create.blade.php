<x-app-layout>
    <x-slot name="header">
        <h2 style="
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        ">
            🎫 Buat Tiket Baru
        </h2>
    </x-slot>

    <div style="padding-top: 2rem; background-color:rgb(8, 27, 47); min-height: 100vh;">
        <div style="
            max-width: 720px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        ">
            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if (Auth::user()->hasRole('admin'))
        {{-- Hanya admin yang melihat dan memilih perusahaan serta sub-client --}}
        <div style="margin-bottom: 1.5rem;">
            <label for="client_id" style="...">🏢 Perusahaan / Instansi</label>
            <select name="client_id" id="client_id" required style="...">
                <option value="" disabled selected>-- Pilih Perusahaan --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="sub_client_id" style="...">👤 Karyawan / Sub Client</label>
            <select name="sub_client_id" id="sub_client_id" required disabled style="...">
                <option value="" disabled selected>-- Pilih Karyawan --</option>
            </select>
        </div>
    @else
        {{-- Untuk user biasa, kirim ID-nya secara otomatis --}}
        <input type="hidden" name="client_id" value="{{ Auth::user()->company_id }}">
        <input type="hidden" name="sub_client_id" value="{{ Auth::user()->id }}">
    @endif

                <div style="margin-bottom: 1.5rem;">
                    <label for="subject" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1f2937;">📝 Judul Tiket</label>
                    <input type="text" name="subject" id="subject" required
                           style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 0.75rem; font-size: 1rem; background-color: #f9fafb;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2)'"
                           onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1f2937;">💬 Pesan</label>
                    <textarea name="message" id="message" rows="6" required
                              style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 0.75rem; font-size: 1rem; background-color: #fefefe; resize: vertical;"
                              onfocus="this.style.borderColor='#06b6d4'; this.style.boxShadow='0 0 0 3px rgba(6,182,212,0.2)'"
                              onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'"></textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="urgency" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1f2937;">⚡ Tingkat Urgensi</label>
                    <select name="urgency" id="urgency" required
        style="..." >
    <option value="" disabled {{ old('urgency') ? '' : 'selected' }}>-- Pilih Urgensi --</option>
    <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>🟢 Low</option>
    <option value="medium" {{ old('urgency') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
    <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>🔴 High</option>
    <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>🔥 Urgent</option>
</select>

                </div>

                {{-- Lampiran --}}
<div style="margin-bottom: 1.5rem;">
    <label for="attachment" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1f2937;">📎 Lampiran</label>

    <label for="attachment"
           style="
               display: flex;
               flex-direction: column;
               justify-content: center;
               align-items: center;
               gap: 0.5rem;
               cursor: pointer;
               border: 2px dashed #cbd5e1;
               border-radius: 1rem;
               background-color: #f9fafb;
               padding: 2rem;
               transition: border-color 0.3s ease;
               text-align: center;
           "
           onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#cbd5e1'">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             style="width: 40px; height: 40px; color: #60a5fa;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 0L8 8m4-4l4 4" />
        </svg>
        <span style="color: #6b7280; font-size: 0.95rem;">Klik atau seret file gambar ke sini</span>
        <input type="file" name="attachment" id="attachment" accept="image/*"
               style="display: none;" onchange="previewImage(event)">
    </label>

    <div id="preview-container" style="margin-top: 1rem; position: relative;">
        <img id="image-preview" src="#" alt="Preview"
             style="max-width: 100%; max-height: 300px; display: none; border-radius: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />
        <button type="button" id="remove-image" onclick="removeImage()"
                style="display: none; position: absolute; top: 10px; right: 10px; background-color: #ef4444; color: white; border: none; padding: 0.4rem 0.6rem; border-radius: 0.4rem; font-weight: bold; cursor: pointer;">
            ✖ Hapus Gambar
        </button>
    </div>
</div>


                {{-- Tombol --}}
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem;">
                    <a href="{{ url()->previous() }}"
                       style="padding: 0.6rem 1.2rem; background-color: #6b7280; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none;"
                       onmouseover="this.style.backgroundColor='#4b5563'" onmouseout="this.style.backgroundColor='#6b7280'">
                        ← Kembali
                    </a>
                    <button type="submit"
                            style="padding: 0.6rem 1.5rem; background: linear-gradient(to right, #2563eb, #3b82f6); color: white; font-weight: 600; border: none; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Kirim Tiket →
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script --}}
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');
            const removeBtn = document.getElementById('remove-image');
            const file = input.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    removeBtn.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
                removeBtn.style.display = 'none';
            }
        }

        function removeImage() {
            const preview = document.getElementById('image-preview');
            const removeBtn = document.getElementById('remove-image');
            const fileInput = document.getElementById('attachment');

            preview.src = '#';
            preview.style.display = 'none';
            removeBtn.style.display = 'none';
            fileInput.value = '';
        }

        document.getElementById('client_id')?.addEventListener('change', function () {
            const clientId = this.value;
            const baseUrl = window.location.origin;
            const subClientSelect = document.getElementById('sub_client_id');

            subClientSelect.innerHTML = '<option value="" disabled selected>Memuat karyawan...</option>';
            subClientSelect.disabled = true;

            fetch(`${baseUrl}/api/clients/${clientId}/users`)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil data');
                    return response.json();
                })
                .then(data => {
                    subClientSelect.innerHTML = '<option value="" disabled selected>-- Pilih Karyawan --</option>';
                    if (data.length === 0) {
                        subClientSelect.innerHTML += '<option value="" disabled>Tidak ada karyawan tersedia</option>';
                    } else {
                        data.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.name;
                            subClientSelect.appendChild(option);
                        });
                    }
                    subClientSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    subClientSelect.innerHTML = '<option value="" disabled>Gagal memuat data</option>';
                    alert('Terjadi kesalahan saat memuat data karyawan.');
                });
        });
    </script>
</x-app-layout>