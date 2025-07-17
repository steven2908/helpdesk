<?php

namespace App\Events;

use App\Models\TicketReply;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketReplyCreatedTelegram
{
    use Dispatchable, SerializesModels;

    public $reply;

    public function __construct(TicketReply $reply)
    {
        $this->reply = $reply;
    }
}
