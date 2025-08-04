<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📱 Scan QR WhatsApp
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-6 text-center">
                <!-- Status -->
                <div id="status-box" class="mb-4 text-sm sm:text-base font-medium text-gray-700">
                    ⏳ Mengecek status bot...
                </div>

                <!-- QR -->
                <div id="qr-box" class="flex justify-center items-center min-h-[200px]">
                    <p class="text-gray-500">Memuat QR...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function fetchQR() {
            const qrBox = document.getElementById('qr-box');
            try {
                const res = await fetch('/api/wa-qr');
                if (!res.ok) {
                    qrBox.innerHTML = '<p class="text-red-500">❌ QR belum tersedia atau sudah kadaluarsa.</p>';
                    return;
                }

                const data = await res.json();
                qrBox.innerHTML = `<img src="${data.qr_image}" alt="QR WhatsApp" class="mx-auto border-4 border-gray-300 rounded-lg max-w-full sm:max-w-xs shadow-md" />`;
            } catch (error) {
                qrBox.innerHTML = '<p class="text-yellow-600">⚠️ Gagal memuat QR.</p>';
                console.error(error);
            }
        }

        async function fetchStatus() {
            const statusBox = document.getElementById('status-box');
            try {
                const res = await fetch('/api/wa-status');
                const data = await res.json();

                if (data.status === 'ready') {
                    statusBox.innerHTML = '<span class="text-green-600">✅ Bot WhatsApp sudah aktif dan login.</span>';
                    document.getElementById('qr-box').innerHTML = '';
                } else if (data.status === 'waiting') {
                    statusBox.innerHTML = '<span class="text-blue-600">📡 Bot belum login. Silakan scan QR di bawah ini.</span>';
                    fetchQR();
                } else {
                    statusBox.innerHTML = '<span class="text-red-600">❌ Tidak ada QR tersedia atau bot offline.</span>';
                }

            } catch (error) {
                statusBox.innerHTML = '<span class="text-yellow-600">⚠️ Tidak dapat mengecek status bot.</span>';
                console.error(error);
            }
        }

        fetchStatus();
        setInterval(fetchStatus, 5000);
    </script>
</x-app-layout>
