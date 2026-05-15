<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::remember('site_settings', 300, fn() =>
            SiteSetting::pluck('value', 'key')->toArray()
        );
        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('site_settings');
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('site_settings');
    }

    /**
     * Return the URL for a stored file path.
     * If a CDN base URL is configured in site settings, it will be used
     * instead of the local storage disk URL.
     *
     * Usage:  Settings::storageUrl($model->image)
     */
    public static function storageUrl(?string $path): string
    {
        if (!$path) return '';
        $cdn = static::get('cdn_base_url');
        if ($cdn) {
            $cdn = rtrim($cdn, '/');
            return $cdn . '/' . ltrim($path, '/');
        }
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}
