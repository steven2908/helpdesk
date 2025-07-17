<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'message',
        'urgency',
        'status',
        'user_id',
        'company_id',
        'attachment',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'response_at' => 'datetime',
        'solved_at' => 'datetime', // ✅ evaluasi SLA penyelesaian
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            // Generate ticket_id unik 5 digit
            do {
                $randomId = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            } while (self::where('ticket_id', $randomId)->exists());

            $ticket->ticket_id = $randomId;
        });
    }

    // ===== RELATIONS =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function getRouteKeyName()
    {
        return 'ticket_id';
    }

    // ===== SLA FUNCTIONS =====

    public function getRemainingResponseTimeAttribute()
    {
        if (!$this->company || is_null($this->company->sla_response_time)) {
            return null;
        }

        $deadline = $this->created_at->copy()->addMinutes($this->company->sla_response_time);

        return Carbon::now()->diffForHumans($deadline, [
            'parts' => 2,
            'syntax' => Carbon::DIFF_ABSOLUTE,
            'short' => true,
        ]);
    }

    public function getSlaResponseStatusAttribute()
    {
        if (!$this->company || is_null($this->company->sla_response_time)) {
            return null;
        }

        $deadline = $this->created_at->copy()->addMinutes($this->company->sla_response_time);

        if ($this->response_at) {
            return $this->response_at->lte($deadline) ? 'Tepat Waktu' : 'Terlambat';
        }

        return now()->lte($deadline) ? 'Belum Terlambat' : 'Terlambat';
    }

    public function getActualResponseTimeAttribute()
    {
        if (!$this->response_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->response_at);
    }

    public function getSlaResolutionStatusAttribute()
    {
        if (!$this->company || !$this->solved_at || is_null($this->company->sla_resolution_time)) {
            return null;
        }

        $deadline = $this->created_at->copy()->addMinutes($this->company->sla_resolution_time);

        return $this->solved_at->lte($deadline) ? 'Tepat Waktu' : 'Terlambat';
    }

    public function getActualResolutionTimeAttribute()
    {
        if (!$this->solved_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->solved_at);
    }
}
