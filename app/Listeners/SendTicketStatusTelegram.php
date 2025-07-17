<?php

namespace App\Listeners;

use App\Events\TicketStatusUpdatedTelegram;
use App\Helpers\TelegramHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Attributes\WithoutQueue;

class SendTicketStatusTelegram
{
    #[WithoutQueue] // ✅ Ini adalah cara Laravel 12 menandai listener tanpa queue
    public function handle(TicketStatusUpdatedTelegram $event)
    {
        static $already = [];

        $key = $event->ticket->id . '-' . md5($event->statusMessage);

        if (isset($already[$key])) {
            Log::info('⛔️ Duplikat listener dilewati', ['ticket_id' => $event->ticket->id]);
            return;
        }

        $already[$key] = true;

        Log::info('✅ Listener dijalankan', ['ticket_id' => $event->ticket->id]);

        $ticket = $event->ticket;

        if ($ticket->user && $ticket->user->telegram_chat_id) {
            TelegramHelper::sendMessage(
                $ticket->user->telegram_chat_id,
                $event->statusMessage
            );
        }
    }
}
