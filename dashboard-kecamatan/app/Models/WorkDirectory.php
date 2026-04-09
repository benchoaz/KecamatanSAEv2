<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDirectory extends Model
{
    use HasFactory, \App\Traits\OperationalStatus;

    protected $table = 'work_directory';

    protected $fillable = [
        'display_name',
        'job_category',
        'job_type',
        'job_title',
        'price',
        'service_area',
        'service_time',
        'contact_phone',
        'owner_pin',
        'last_toggle_at',
        'short_description',
        'data_source',
        'consent_public',
        'status',
        'operating_hours',
        'is_on_holiday',
    ];

    protected $casts = [
        'consent_public' => 'boolean',
        'is_on_holiday' => 'boolean',
    ];

    /**
     * Get available categories
     */
    public static function getCategories()
    {
        return [
            // Jasa Harian
            'Tukang Pijat',
            'Tukang Bangunan',
            'Buruh Tani',
            'Tukang Gali',
            'Tukang Potong Rumput',
            'Jasa Rumah Tangga',
            'Tukang Rantai',
            'Tukang Las',
            'Tukang Kayu',
            'Montir',
            'Salon / Potong Rambut',
            'Laundry',

            // Transportasi
            'Ojek',
            'Becak',
            'Sopir',
            'Angkot',

            // Keliling
            'Tukang Sayur',
            'Penjual Ikan',
            'Penjual Tahu Tempe',
            'Penjual Gas',
            'Penjual Bubur',
            'Kue Keliling',

            // Lainnya
            'Lainnya'
        ];
    }

    /**
     * Scope untuk data yang aktif dan izin publik
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'active')
            ->where('consent_public', true);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('job_category', $category);
    }

    /**
     * Get icon based on job type
     */
    public function getIconAttribute()
    {
        $icons = [
            // Jasa Harian
            'Tukang Pijat' => 'fa-hand-sparkles',
            'Tukang Bangunan' => 'fa-hammer',
            'Buruh Tani' => 'fa-seedling',
            'Tukang Gali' => 'fa-shovel',
            'Tukang Potong Rumput' => 'fa-leaf',
            'Jasa Rumah Tangga' => 'fa-broom',

            // Transportasi
            'Ojek' => 'fa-motorcycle',
            'Becak' => 'fa-bicycle',
            'Sopir' => 'fa-truck',

            // Keliling
            'Tukang Sayur' => 'fa-carrot',
            'Penjual Ikan' => 'fa-fish',
            'Penjual Tahu Tempe' => 'fa-cube',
            'Penjual Gas' => 'fa-fire',
        ];

        foreach ($icons as $keyword => $icon) {
            if (stripos($this->job_title, $keyword) !== false) {
                return $icon;
            }
        }

        // Default icons by type
        return match ($this->job_type) {
            'transportasi' => 'fa-car',
            'keliling' => 'fa-shopping-cart',
            'jasa' => 'fa-tools',
            default => 'fa-briefcase',
        };
    }

    /**
     * Format phone for WhatsApp
     */
    public function getWhatsappLinkAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->contact_phone);

        // Normalize to 62 format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return "https://wa.me/{$phone}";
    }
}
