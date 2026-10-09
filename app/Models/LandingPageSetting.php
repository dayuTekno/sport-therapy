<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $table = 'landing_page_settings';

    protected $guarded = [];

    protected $casts = [
        'content_json' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Dapatkan konten section berdasarkan key dengan fallback
     */
    public static function getSection(string $key, array $default = []): self
    {
        return self::firstOrCreate(
            ['section_key' => $key],
            [
                'title' => $default['title'] ?? null,
                'subtitle' => $default['subtitle'] ?? null,
                'content_json' => $default['content_json'] ?? null,
                'image_path' => $default['image_path'] ?? null,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
