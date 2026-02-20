<?php

namespace App\Services;

/**
 * Owner PIN Service
 * 
 * Handles PIN generation, validation, and management for UMKM/Jasa owners
 */
class OwnerPinService
{
    private string $cachePath;

    public function __construct()
    {
        $this->cachePath = __DIR__ . '/../../storage/framework/owner_pins';
        $this->ensureDirectoryExists();
    }

    /**
     * Generate a new 6-digit PIN
     */
    public function generatePin(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store PIN for a phone number
     */
    public function storePin(string $phone, string $pin, string $listingType, int $listingId): void
    {
        $phone = $this->normalizePhone($phone);
        $key = $this->generateKey($phone);
        $file = $this->cachePath . '/' . $key . '.json';

        // Check if entry exists
        $data = $this->getPinData($phone);

        // Add or update listing
        $listings = $data['listings'] ?? [];
        $listings[] = [
            'id' => $listingId,
            'type' => $listingType,
            'pin' => password_hash($pin, PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $data = [
            'phone' => $phone,
            'listings' => $listings,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Verify PIN for a phone number
     */
    public function verifyPin(string $phone, string $pin): array
    {
        $phone = $this->normalizePhone($phone);
        $data = $this->getPinData($phone);

        if (empty($data) || empty($data['listings'])) {
            return [
                'success' => false,
                'message' => 'Nomor tidak terdaftar sebagai owner'
            ];
        }

        foreach ($data['listings'] as $listing) {
            if (password_verify($pin, $listing['pin'])) {
                return [
                    'success' => true,
                    'data' => [
                        'owner_phone' => $phone,
                        'listings' => array_map(function ($l) {
                            return [
                                'id' => $l['id'],
                                'type' => $l['type']
                            ];
                        }, $data['listings'])
                    ]
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'PIN salah'
        ];
    }

    /**
     * Reset PIN for a phone number (generate new)
     */
    public function resetPin(string $phone): array
    {
        $phone = $this->normalizePhone($phone);
        $data = $this->getPinData($phone);

        if (empty($data) || empty($data['listings'])) {
            return [
                'success' => false,
                'message' => 'Nomor tidak terdaftar sebagai owner'
            ];
        }

        // Generate new PIN
        $newPin = $this->generatePin();

        // Update all listings with new PIN
        foreach ($data['listings'] as &$listing) {
            $listing['pin'] = password_hash($newPin, PASSWORD_BCRYPT);
            $listing['pin_reset_at'] = date('Y-m-d H:i:s');
        }

        // Save updated data
        $key = $this->generateKey($phone);
        $file = $this->cachePath . '/' . $key . '.json';
        $data['updated_at'] = date('Y-m-d H:i:s');
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        return [
            'success' => true,
            'pin' => $newPin,
            'listings' => array_map(function ($l) {
                return [
                    'id' => $l['id'],
                    'type' => $l['type']
                ];
            }, $data['listings'])
        ];
    }

    /**
     * Get PIN data for a phone number
     */
    public function getPinData(string $phone): ?array
    {
        $phone = $this->normalizePhone($phone);
        $key = $this->generateKey($phone);
        $file = $this->cachePath . '/' . $key . '.json';

        if (!file_exists($file)) {
            return null;
        }

        return json_decode(file_get_contents($file), true);
    }

    /**
     * Check if phone is registered as owner
     */
    public function isOwner(string $phone): bool
    {
        $data = $this->getPinData($phone);
        return !empty($data) && !empty($data['listings']);
    }

    /**
     * Get all listings for a phone number
     */
    public function getListings(string $phone): array
    {
        $data = $this->getPinData($phone);

        if (empty($data) || empty($data['listings'])) {
            return [];
        }

        return array_map(function ($l) {
            return [
                'id' => $l['id'],
                'type' => $l['type']
            ];
        }, $data['listings']);
    }

    /**
     * Remove listing from owner
     */
    public function removeListing(string $phone, int $listingId, string $listingType): bool
    {
        $phone = $this->normalizePhone($phone);
        $data = $this->getPinData($phone);

        if (empty($data) || empty($data['listings'])) {
            return false;
        }

        // Filter out the listing
        $data['listings'] = array_filter($data['listings'], function ($l) use ($listingId, $listingType) {
            return !($l['id'] == $listingId && $l['type'] == $listingType);
        });

        // Re-index array
        $data['listings'] = array_values($data['listings']);

        // If no listings left, delete the file
        if (empty($data['listings'])) {
            $key = $this->generateKey($phone);
            $file = $this->cachePath . '/' . $key . '.json';
            if (file_exists($file)) {
                unlink($file);
            }
            return true;
        }

        // Save updated data
        $key = $this->generateKey($phone);
        $file = $this->cachePath . '/' . $key . '.json';
        $data['updated_at'] = date('Y-m-d H:i:s');
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * Normalize phone number
     */
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Generate cache key from phone
     */
    private function generateKey(string $phone): string
    {
        return md5($this->normalizePhone($phone));
    }

    /**
     * Ensure cache directory exists
     */
    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }
}
