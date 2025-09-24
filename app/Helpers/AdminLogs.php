<?php
namespace App\Helpers;

use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;

class AdminLogs
{
    public static function store($content, $userId = null, $ip_address = null)
    {
        AdminLog::create([
            'content'    => $content,
            'user_id'    => $userId ?? Auth::id(),
            'ip_address' => $ip_address ?? request()->ip(),
        ]);
    }
}
