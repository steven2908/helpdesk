<?php

namespace App\Helpers;

use Carbon\Carbon;

class SlaHelper
{
    /**
     * Hitung durasi kerja (dalam menit) antara dua waktu,
     * mengabaikan jam istirahat yang bisa dikonfigurasi,
     * akhir pekan, dan di luar jam kerja (08:00 - 17:00).
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param array $breaks Format: [['HH:MM', 'HH:MM']]
     * @return int
     */
    public static function calculateWorkingMinutes(Carbon $start, Carbon $end, array $breaks = [['13:05', '13:10']]): int
    {
        if ($start >= $end) {
            return 0;
        }

        $totalMinutes = 0;
        $current = $start->copy()->second(0);
        $end = $end->copy()->second(0);

        while ($current < $end) {
            if ($current->isWeekend()) {
                $current->addDay()->startOfDay();
                continue;
            }

            $hour = $current->hour;

            if ($hour < 8 || $hour >= 17) {
                $current->addMinute();
                continue;
            }

            // Skip jika berada dalam waktu istirahat
            $isBreakTime = false;
            foreach ($breaks as [$breakStartStr, $breakEndStr]) {
                [$breakStartHour, $breakStartMinute] = explode(':', $breakStartStr);
                [$breakEndHour, $breakEndMinute] = explode(':', $breakEndStr);

                $breakStart = $current->copy()->setTime((int)$breakStartHour, (int)$breakStartMinute, 0);
                $breakEnd = $current->copy()->setTime((int)$breakEndHour, (int)$breakEndMinute, 0);

                if ($current >= $breakStart && $current < $breakEnd) {
                    $current = $breakEnd; // langsung lompat ke akhir istirahat
                    $isBreakTime = true;
                    break;
                }
            }

            if ($isBreakTime) {
                continue;
            }

            $totalMinutes++;
            $current->addMinute();
        }

        return $totalMinutes;
    }

    /**
     * Evaluasi SLA berdasarkan durasi kerja aktual terhadap batas SLA.
     *
     * @param Carbon $startAt
     * @param Carbon|null $endAt
     * @param int $slaLimit dalam menit
     * @param array $breaks
     * @return string
     */
    public static function evaluateSla(Carbon $startAt, ?Carbon $endAt, int $slaLimit, array $breaks = [['13:05', '13:10']]): string
    {
        if (is_null($endAt)) {
            return 'Belum Diselesaikan';
        }

        $actualMinutes = self::calculateWorkingMinutes($startAt, $endAt, $breaks);

        return $actualMinutes <= $slaLimit ? 'Tepat Waktu' : 'Terlambat';
    }
}
