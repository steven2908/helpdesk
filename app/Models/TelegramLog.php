<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramLog extends Model
{
    protected $fillable = ['chat_id', 'message', 'raw'];
    protected $casts = [
        'raw' => 'array',
    ];
}
