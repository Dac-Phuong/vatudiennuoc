<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'value',
    ];
     public static function updateOption(string $key, $value, ?string $domain = null): self
    {
        try {
            // Nếu là array → serialize
            if (is_array($value)) {
                $value = serialize($value);
            }

            if (! is_null($value)) {
                $value = (string) $value;
            }

            return self::updateOrCreate(
                [
                    'key'   => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Option::updateOption failed', [
                'key'   => $key,
                'value'  => $value,
                'error'  => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get option value (multi-domain)
     */
    public static function getOption(string $key, $default = null, ?string $domain = null)
    {
        if ($key === '') {
            return $default;
        }

        $item = self::where('key', $key)->first();

        $value = $item ? $item->value : null;

        // Check if value is serialized
        if ($value !== null && self::isSerialized($value)) {
            return unserialize($value);
        }

        return $value ?? $default;
    }

    /**
     * Check if a string is serialized
     */
    private static function isSerialized($data): bool
    {
        if (! is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ($data === 'N;') {
            return true;
        }
        if (! preg_match('/^([adObis]):/', $data, $matches)) {
            return false;
        }
        return @unserialize($data) !== false;
    }
}
