<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings_all'));
        static::deleted(fn () => Cache::forget('settings_all'));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::all_cached();
        return $settings[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function all_cached(): array
    {
        return Cache::remember('settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
