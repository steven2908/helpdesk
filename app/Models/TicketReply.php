<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $message
 * @property string|null $attachment
 * @property string $visibility
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ticket $ticket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketReply whereVisibility($value)
 * @mixin \Eloquent
 */
class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'visibility',
        'attachment',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userRole()
{
    return $this->user?->roles()?->first(); // via Spatie
}


}
