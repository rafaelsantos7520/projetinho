<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSettings extends Model
{
    public function getConnectionName(): ?string
    {
        return config('tenancy.tenant_connection', config('database.default'));
    }

    protected $table = 'store_settings';

    protected $fillable = [
        'logo_url',
        'primary_color',
        'whatsapp_number',
        'instagram_url',
        'facebook_url',
        'biography',
        'banner_1_url',
        'banner_2_url',
        'banner_3_url',
    ];

    protected static ?self $currentCache = null;

    public static function current(): self
    {
        if (static::$currentCache !== null) {
            return static::$currentCache;
        }

        static::$currentCache = static::firstOrCreate([], [
            'primary_color' => '#0f172a',
        ]);

        return static::$currentCache;
    }

    /**
     * Clear the cache (useful after updates)
     */
    public static function clearCache(): void
    {
        static::$currentCache = null;
    }
}
