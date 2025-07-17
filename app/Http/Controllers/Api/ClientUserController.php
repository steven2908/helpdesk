<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientUserController extends Controller
{
    public function getUsers($clientId)
    {
        $users = User::where('company_id', $clientId)->get(['id', 'name']);
        return response()->json($users);
    }
}
