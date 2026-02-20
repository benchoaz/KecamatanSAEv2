<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get setting value with type casting
     */
    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Scope for specific module
     */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for specific key
     */
    public function scopeWithKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get a setting value by module and key
     */
    public static function getValue(string $module, string $key, $default = null)
    {
        $setting = static::where('module', $module)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->typed_value : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $module, string $key, $value, string $type = 'string', ?string $description = null): self
    {
        // Convert value based on type
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        return static::updateOrCreate(
            ['module' => $module, 'key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
