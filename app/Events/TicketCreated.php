<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;
use App\Models\Ticket;

class TicketCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        return new Channel('staff.ticket');
    }

    public function broadcastAs()
    {
        return 'ticket.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'user' => $this->ticket->user->name ?? 'Anonymous',
        ];
    }
}
