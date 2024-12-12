<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdminSetting extends Model
{
    protected $fillable = ['name', 'key', 'value'];

    // Cache duration in minutes
    const CACHE_DURATION = 60;

    public static function get($key, $default = null)
    {
        $cacheKey = "admin_setting_{$key}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            $setting = self::create([
                'name' => ucwords(str_replace('.', ' ', $key)),
                'key' => $key,
                'value' => $value
            ]);
        }

        Cache::forget("admin_setting_{$key}");
        return $setting;
    }
} 