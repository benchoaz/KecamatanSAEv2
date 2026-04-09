<?php

namespace App\Traits;

use Carbon\Carbon;

trait OperationalStatus
{
    /**
     * Check if the business/service is currently open
     */
    public function isOpen()
    {
        // 1. Check if manually set to holiday (Mode Libur)
        if ($this->is_on_holiday) {
            return false;
        }

        // 2. If no operating hours set, assume always open (default behavior)
        if (!$this->operating_hours || trim($this->operating_hours) === '') {
            return true;
        }

        try {
            // Support formats like "08:00 - 17:00" or "08.00-17.00"
            $cleanHours = str_replace('.', ':', $this->operating_hours);
            if (preg_match('/(\d{2}:\d{2})\s?-\s?(\d{2}:\d{2})/', $cleanHours, $matches)) {
                $now = Carbon::now()->format('H:i');
                $start = $matches[1];
                $end = $matches[2];

                if ($start <= $end) {
                    return $now >= $start && $now <= $end;
                } else {
                    // Over midnight case (e.g. 22:00 - 04:00)
                    return $now >= $start || $now <= $end;
                }
            }
        } catch (\Exception $e) {
            return true;
        }

        return true;
    }

    /**
     * Get human-friendly operational status
     */
    public function getOperationalStatusAttribute()
    {
        if ($this->is_on_holiday) {
            return [
                'label' => 'Lagi Libur',
                'color' => 'rose',
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-600',
                'is_open' => false,
                'icon' => 'fa-calendar-times'
            ];
        }

        if ($this->isOpen()) {
            return [
                'label' => 'Lagi Buka',
                'color' => 'emerald',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
                'is_open' => true,
                'icon' => 'fa-clock'
            ];
        }

        return [
            'label' => 'Sudah Tutup',
            'color' => 'slate',
            'bg' => 'bg-slate-50',
            'text' => 'text-slate-600',
            'is_open' => false,
            'icon' => 'fa-moon'
        ];
    }
}
