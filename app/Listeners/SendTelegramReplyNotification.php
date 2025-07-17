<?php

namespace App\Listeners;

use App\Events\TicketReplyCreatedTelegram;
use Illuminate\Support\Facades\Http;

class SendTelegramReplyNotification
{
    public function handle(TicketReplyCreatedTelegram $event)
    {
        \Log::info('Listener SendTelegramReplyNotification dipanggil', [
            'reply_id' => $event->reply->id,
            'ticket_id' => $event->reply->ticket_id,
            'sender_id' => $event->reply->user_id,
            'recipient_id' => optional($event->reply->ticket)->user_id,
            'recipient_chat_id' => optional($event->reply->ticket->user)->telegram_chat_id,
        ]);

        $reply = $event->reply;
        $ticket = $reply->ticket;
        $sender = $reply->user;
        $recipient = $ticket->user;

        // Cegah user dapat notifikasi jika dia sendiri yang balas
        if ($sender->id === $recipient->id) {
            return;
        }

        if ($recipient->telegram_chat_id) {
            $message = "📩 Balasan dari Admin/Staff untuk Tiket #{$ticket->ticket_id}:\n\n"
                     . $reply->message;

            $token = config('services.telegram.bot_token');
            $url = "https://api.telegram.org/bot{$token}/sendMessage";

            // ✅ Log token & URL untuk debug
            \Log::info('Telegram bot token digunakan', ['token' => $token]);
            \Log::info('URL Telegram dipakai', ['url' => $url]);

            $response = Http::post($url, [
                'chat_id' => $recipient->telegram_chat_id,
                'text' => $message,
            ]);

            \Log::info('Telegram API response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
