<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WASession extends Model
{
    protected $table = 'wa_sessions'; // Pastikan sesuai dengan nama tabel

    protected $fillable = [
        'phone',
        'step',
        'data',
    ];

    protected $casts = [
        'data' => 'array', // Biar kolom `data` bisa langsung dipakai sebagai array
    ];
}
