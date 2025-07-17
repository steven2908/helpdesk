<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\TicketCreatedTelegram;
use App\Listeners\SendTelegramTicketNotification;
use App\Events\TicketStatusUpdatedTelegram;
use App\Listeners\SendTicketStatusTelegram;
use App\Events\TicketReplyCreatedTelegram;
use App\Listeners\SendTelegramReplyNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketCreatedTelegram::class => [
            SendTelegramTicketNotification::class,
        ],

        TicketStatusUpdatedTelegram::class => [
            SendTicketStatusTelegram::class,
        ],

        TicketReplyCreatedTelegram::class => [
            SendTelegramReplyNotification::class,
        ],
    ];

    public static $shouldDiscoverEvents = false;

    public function boot(): void
    {
        //
    }
}
