<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class WhatsappHelper
{
    public static function send($number, $message)
    {
        // Nomor harus tanpa + dan pakai format 62 (bukan 08)
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        try {
            Http::post('http://localhost:3000/send-message', [
                'nomor' => $number,
                'pesan' => $message,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('[WA Error] Gagal kirim ke ' . $number . ': ' . $e->getMessage());
        }
    }
}
