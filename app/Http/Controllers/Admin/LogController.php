<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogController extends Controller
{
    public $adminLogService;
    public function __construct()
    {
        $this->adminLogService = new AdminLogService();
    }
    public function adminLogService()
    {
        return app(AdminLogService::class);
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $logs = $this->adminLogService()->filterDataTable($data);
        return Inertia::render('Admin/Logs/Index', [
            'data'    => $logs,
            'filters' => $request->only('search'),
        ]);
    }
    public function destroy(int $id)
    {
        $this->adminLogService()->delete($id);
        return redirect()->back();
    }
}
