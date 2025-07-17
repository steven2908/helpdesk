<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramLog;

class TelegramLogController extends Controller
{
    public function index()
    {
        $logs = TelegramLog::latest()->paginate(20); // bisa juga pakai ->get()
        return view('admin.telegram_logs.index', compact('logs'));
    }
}
