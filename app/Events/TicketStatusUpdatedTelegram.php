<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdatedTelegram
{
    use Dispatchable, SerializesModels;

    public $ticket;
    public $statusMessage;

    public function __construct(Ticket $ticket, string $statusMessage)
    {
        $this->ticket = $ticket;
        $this->statusMessage = $statusMessage;
    }
}
