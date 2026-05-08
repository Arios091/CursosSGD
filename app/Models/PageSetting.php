<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    // Obtener un valor por clave
    public static function getValue($key, $default = null)
    {
        $settings = Cache::rememberForever('page_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    // Actualizar o crear un valor
    public static function setValue($key, $value, $type = 'text')
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
        Cache::forget('page_settings');
    }

    // Obtener todos como array
    public static function getAll()
    {
        return Cache::rememberForever('page_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('page_settings');
        });
        static::deleted(function () {
            Cache::forget('page_settings');
        });
    }
}