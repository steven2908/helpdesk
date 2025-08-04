<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WAQRController extends Controller
{

    public function index()
{
    if (Cache::get('wa_logged_in')) {
        return view('waqr', [
            'status' => 'ready',
            'message' => '✅ Bot WhatsApp sudah aktif dan siap digunakan.',
            'qr' => null
        ]);
    }

    $qr = Cache::get('wa_qr_image');

    if ($qr) {
        return view('waqr', [
            'status' => 'need_scan',
            'message' => '📡 Bot belum login. Silakan scan QR di bawah ini.',
            'qr' => $qr
        ]);
    }

    return view('waqr', [
        'status' => 'no_qr',
        'message' => '❌ QR belum tersedia atau sudah kadaluarsa.',
        'qr' => null
    ]);
}


    // Simpan QR yang dikirim dari bot.js
    public function store(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|string',
        ]);

        // Simpan QR base64 image ke cache (selama 1 menit)
        Cache::put('wa_qr_image', $request->qr_image, 60);

        return response()->json(['status' => 'QR disimpan']);
    }

    // Endpoint API (jika frontend fetch pakai JS)
    public function show()
    {
        $qr = Cache::get('wa_qr_image');

        if (!$qr) {
            return response()->json(['message' => 'QR belum tersedia'], 404);
        }

        return response()->json(['qr_image' => $qr]);
    }

    // Untuk view Blade
    public function showQR()
    {
        $qrImage = Cache::get('wa_qr_image');

        return view('wa.qr', ['qrImage' => $qrImage]);
    }

    public function status()
{
    if (Cache::get('wa_logged_in')) {
        return response()->json(['status' => 'ready', 'message' => 'Bot sudah aktif']);

    }

    $qr = Cache::get('wa_qr_image');

    if ($qr) {
        return response()->json(['status' => 'waiting', 'message' => 'Silakan scan QR']);
    }

    return response()->json(['status' => 'not_available', 'message' => 'QR belum tersedia atau sudah kadaluarsa']);
}



public function updateStatus(Request $request)
{
    if ($request->status === 'ready') {
        Cache::put('wa_logged_in', true, now()->addMinutes(10));
    }

    return response()->json(['message' => 'Status disimpan']);
}


}
