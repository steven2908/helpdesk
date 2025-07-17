<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            📜 Log Pesan Telegram
        </h2>
    </x-slot>

    <div class="p-4">
        <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full table-auto text-sm text-left">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3 border">Waktu</th>
                        <th class="px-4 py-3 border">Chat ID</th>
                        <th class="px-4 py-3 border">Pesan</th>
                        <th class="px-4 py-3 border w-1/2">Payload</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 dark:text-gray-100">
                    @forelse ($logs as $log)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 border">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-2 border font-mono text-blue-700 dark:text-blue-300">{{ $log->chat_id }}</td>
                            <td class="px-4 py-2 border">{{ $log->message }}</td>
                            <td class="px-4 py-2 border text-xs text-gray-600 dark:text-gray-300 max-w-xs overflow-x-auto">
                                <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded max-h-64 overflow-auto">
                                    <pre class="whitespace-pre-wrap break-words text-[11px] leading-snug">{{ json_encode($log->raw, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-4 text-gray-500 dark:text-gray-400">Belum ada log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
