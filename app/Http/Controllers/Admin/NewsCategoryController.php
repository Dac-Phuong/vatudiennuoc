<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsCategory\StoreRequest;
use App\Http\Requests\Admin\NewsCategory\UpdateRequest;
use App\Services\NewsCategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsCategoryController extends Controller
{
    public $newsCategoryService;
    public function __construct()
    {
        $this->newsCategoryService = new NewsCategoryService();
    }
    public function newsCategoryService()
    {
        return app(NewsCategoryService::class);
    }
    public function index(Request $request)
    {
        $data       = $request->all();
        $categories = $this->newsCategoryService()->filterDataTable($data);
        return Inertia::render('Admin/News/Category/Index', [
            'data'    => $categories,
            'filters' => $request->only('search'),
        ]);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $this->newsCategoryService()->create($validated);
        return redirect()->back();
    }
    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $this->newsCategoryService()->update($id, $validated);
        return redirect()->back();
    }
    public function destroy(int $id)
    {
        $this->newsCategoryService()->delete($id);
        return redirect()->back();
    }
}
