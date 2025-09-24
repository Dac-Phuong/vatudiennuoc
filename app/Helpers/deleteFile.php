<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class DeleteFile
{
    public static function delete(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_starts_with($path, url('storage'))) {
            $path = str_replace(url('storage') . '/', '', $path);
        }
        Storage::disk('public')->delete($path);
    }
}
