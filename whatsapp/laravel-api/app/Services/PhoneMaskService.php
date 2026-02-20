<?php

namespace App\Services;

/**
 * Phone Number Masking Service
 * 
 * Masks phone numbers for public display to protect privacy
 */
class PhoneMaskService
{
    /**
     * Mask phone number for public display
     * 
     * Example: 6281234567890 -> 62812****7890
     * 
     * @param string $phone Phone number to mask
     * @param int $visibleStart Number of digits visible at start
     * @param int $visibleEnd Number of digits visible at end
     * @return string Masked phone number
     */
    public static function mask(string $phone, int $visibleStart = 4, int $visibleEnd = 4): string
    {
        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($phone);

        // If phone is too short, don't mask
        if ($length <= $visibleStart + $visibleEnd) {
            return $phone;
        }

        $start = substr($phone, 0, $visibleStart);
        $end = substr($phone, -$visibleEnd);
        $masked = str_repeat('*', $length - $visibleStart - $visibleEnd);

        return $start . $masked . $end;
    }

    /**
     * Generate WhatsApp link from phone number
     * 
     * @param string $phone Phone number
     * @param string|null $message Pre-filled message (optional)
     * @return string WhatsApp link
     */
    public static function generateWaLink(string $phone, ?string $message = null): string
    {
        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure phone starts with country code
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $link = "https://wa.me/{$phone}";

        if ($message) {
            $link .= '?text=' . urlencode($message);
        }

        return $link;
    }

    /**
     * Format phone number for display
     * 
     * Example: 6281234567890 -> +62 812-3456-7890
     * 
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    public static function format(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($phone);

        // Indonesian format
        if ($length >= 10 && $length <= 13) {
            if (str_starts_with($phone, '62')) {
                $country = '+62';
                $rest = substr($phone, 2);
            } elseif (str_starts_with($phone, '0')) {
                $country = '+62';
                $rest = substr($phone, 1);
            } else {
                $country = '';
                $rest = $phone;
            }

            // Format based on length
            if (strlen($rest) === 10) {
                $formatted = substr($rest, 0, 3) . '-' . substr($rest, 3, 4) . '-' . substr($rest, 7);
            } elseif (strlen($rest) === 11) {
                $formatted = substr($rest, 0, 4) . '-' . substr($rest, 4, 4) . '-' . substr($rest, 8);
            } else {
                $formatted = $rest;
            }

            return $country . ' ' . $formatted;
        }

        return $phone;
    }

    /**
     * Normalize phone number to international format
     * 
     * @param string $phone Phone number in any format
     * @return string Normalized phone number (62xxx)
     */
    public static function normalize(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08xx to 628xx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // If starts with 8, add 62
        if (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Check if phone number is valid Indonesian number
     * 
     * @param string $phone Phone number
     * @return bool True if valid
     */
    public static function isValidIndonesian(string $phone): bool
    {
        $phone = self::normalize($phone);

        // Indonesian mobile numbers: 628xxxxxxxxxx (10-13 digits total)
        if (preg_match('/^628[0-9]{7,10}$/', $phone)) {
            return true;
        }

        // Indonesian landline: 6221xxxxxxx, 6231xxxxxxx, etc.
        if (preg_match('/^62[0-9]{9,12}$/', $phone)) {
            return true;
        }

        return false;
    }

    /**
     * Get phone type (mobile/landline)
     * 
     * @param string $phone Phone number
     * @return string 'mobile', 'landline', or 'unknown'
     */
    public static function getType(string $phone): string
    {
        $phone = self::normalize($phone);

        // Mobile: starts with 628
        if (str_starts_with($phone, '628')) {
            return 'mobile';
        }

        // Landline: starts with 62 followed by area code
        if (preg_match('/^62[0-9]{2}/', $phone)) {
            return 'landline';
        }

        return 'unknown';
    }

    /**
     * Mask and generate WhatsApp link for public display
     * 
     * @param string $phone Phone number
     * @param string|null $message Pre-filled message
     * @return array Array with 'masked' and 'link' keys
     */
    public static function forPublicDisplay(string $phone, ?string $message = null): array
    {
        return [
            'original' => $phone,
            'masked' => self::mask($phone),
            'formatted' => self::format($phone),
            'link' => self::generateWaLink($phone, $message),
            'type' => self::getType($phone)
        ];
    }
}
