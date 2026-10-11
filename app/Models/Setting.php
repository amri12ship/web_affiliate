<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get every setting as a keyed array.
     *
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        try {
            return static::query()->pluck('value', 'key')->all();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Get a single setting value, falling back to the given default.
     */
    public static function value(string $key, mixed $default = null): mixed
    {
        $value = static::values()[$key] ?? null;

        return $value ?? $default;
    }

    /**
     * Persist a single setting value.
     */
    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Persist multiple settings at once.
     *
     * @param  array<string, mixed>  $settings
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::put($key, $value);
        }
    }
}
