<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nomor_pengirim', 'q1', 'q2', 'q3', 'q4', 'q5', 'saran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
{
    return $this->belongsTo(Ticket::class);
}

}
