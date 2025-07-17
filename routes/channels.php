<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('staff.ticket', function ($user) {
    return in_array($user->role, ['admin', 'staff']);
});

