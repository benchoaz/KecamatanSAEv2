<?php

namespace App\Services;

/**
 * Input Sanitizer Service
 * 
 * Provides input validation and sanitization for WhatsApp bot inputs
 */
class InputSanitizer
{
    /**
     * Sanitize phone number
     * 
     * @param string $phone
     * @return string|null
     */
    public function sanitizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Validate length (10-15 digits for international numbers)
        if (strlen($clean) < 10 || strlen($clean) > 15) {
            return null;
        }

        return $clean;
    }

    /**
     * Sanitize text input
     * 
     * @param string $text
     * @param int $maxLength
     * @return string
     */
    public function sanitizeText(?string $text, int $maxLength = 1000): string
    {
        if (empty($text)) {
            return '';
        }

        // Remove control characters except newlines
        $clean = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Trim whitespace
        $clean = trim($clean);

        // Limit length
        if (strlen($clean) > $maxLength) {
            $clean = substr($clean, 0, $maxLength);
        }

        return $clean;
    }

    /**
     * Sanitize PIN input
     * 
     * @param string $pin
     * @return string|null
     */
    public function sanitizePin(?string $pin): ?string
    {
        if (empty($pin)) {
            return null;
        }

        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $pin);

        // Validate exactly 6 digits
        if (strlen($clean) !== 6) {
            return null;
        }

        return $clean;
    }

    /**
     * Sanitize listing ID
     * 
     * @param mixed $id
     * @return int|null
     */
    public function sanitizeId($id): ?int
    {
        if (empty($id)) {
            return null;
        }

        $int = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        return $int !== false ? $int : null;
    }

    /**
     * Sanitize listing type
     * 
     * @param string $type
     * @return string|null
     */
    public function sanitizeListingType(?string $type): ?string
    {
        if (empty($type)) {
            return null;
        }

        $type = strtolower(trim($type));

        return in_array($type, ['umkm', 'jasa']) ? $type : null;
    }

    /**
     * Sanitize action type
     * 
     * @param string $action
     * @return string|null
     */
    public function sanitizeAction(?string $action): ?string
    {
        if (empty($action)) {
            return null;
        }

        $action = strtolower(trim($action));

        return in_array($action, ['open', 'close']) ? $action : null;
    }

    /**
     * Sanitize search query
     * 
     * @param string $query
     * @return string
     */
    public function sanitizeSearchQuery(?string $query): string
    {
        if (empty($query)) {
            return '';
        }

        // Remove special characters that could be used for injection
        $clean = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $query);

        // Trim and limit length
        $clean = trim(substr($clean, 0, 100));

        return $clean;
    }

    /**
     * Validate WhatsApp chat ID format
     * 
     * @param string $chatId
     * @return bool
     */
    public function isValidWhatsAppChatId(?string $chatId): bool
    {
        if (empty($chatId)) {
            return false;
        }

        // Format: phone@s.whatsapp.net or phone@g.us for groups
        return (bool) preg_match('/^[0-9]+@(s\.whatsapp\.net|g\.us)$/', $chatId);
    }

    /**
     * Extract phone from chat ID
     * 
     * @param string $chatId
     * @return string|null
     */
    public function extractPhoneFromChatId(?string $chatId): ?string
    {
        if (empty($chatId)) {
            return null;
        }

        // Extract phone number from chat ID
        if (preg_match('/^([0-9]+)@/', $chatId, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Sanitize all fields in an array
     * 
     * @param array $data
     * @param array $rules
     * @return array
     */
    public function sanitizeArray(array $data, array $rules): array
    {
        $sanitized = [];

        foreach ($rules as $field => $type) {
            $value = $data[$field] ?? null;

            switch ($type) {
                case 'phone':
                    $sanitized[$field] = $this->sanitizePhone($value);
                    break;
                case 'pin':
                    $sanitized[$field] = $this->sanitizePin($value);
                    break;
                case 'id':
                    $sanitized[$field] = $this->sanitizeId($value);
                    break;
                case 'listing_type':
                    $sanitized[$field] = $this->sanitizeListingType($value);
                    break;
                case 'action':
                    $sanitized[$field] = $this->sanitizeAction($value);
                    break;
                case 'search_query':
                    $sanitized[$field] = $this->sanitizeSearchQuery($value);
                    break;
                case 'text':
                    $sanitized[$field] = $this->sanitizeText($value);
                    break;
                case 'text_short':
                    $sanitized[$field] = $this->sanitizeText($value, 100);
                    break;
                default:
                    $sanitized[$field] = $this->sanitizeText($value);
            }
        }

        return $sanitized;
    }
}
