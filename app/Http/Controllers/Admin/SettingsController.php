<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public $settingService;
    public function __construct()
    {
        $this->settingService = new SettingService();
    }
    public function settingService()
    {
        return app(SettingService::class);
    }
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }
    public function update(Request $request)
    {
        $this->settingService()->updateSetting($request);
        return redirect()->back();
    }
}
