<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSettings extends Model
{
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

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'primary_color' => '#0f172a',
        ]);
    }
}
