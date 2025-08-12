<?php

namespace App\Models;

use illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'technician_name',
        'title',
        'reason',
        'impact',
        'notes',
        'user_id', // ini WAJIB ada
        'image',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

}
