<?php

namespace App\Listeners;

use App\Events\TicketCreatedTelegram;
use Illuminate\Support\Facades\Http;

class SendTelegramTicketNotification
{
    public function handle(TicketCreatedTelegram $event)
    {
        $ticket = $event->ticket;

        $token = env('TELEGRAM_BOT_TOKEN');
        $adminIds = explode(',', env('TELEGRAM_ADMIN_CHAT_ID'));

        $message = "📢 *Tiket Baru Masuk*\n"
                 . "*Dari:* " . ($ticket->user->name ?? '-') . "\n"
                 . "*Subjek:* " . $ticket->subject . "\n"
                 . "*Urgensi:* " . $ticket->urgency . "\n"
                 . "*ID:* #" . $ticket->ticket_id;

        foreach ($adminIds as $adminId) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($adminId),
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        }
    }
}

