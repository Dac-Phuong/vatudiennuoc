<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCategory\StoreRequest;
use App\Http\Requests\Admin\ProductCategory\UpdateRequest;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductCategoryController extends Controller
{
    public $productCategoryService;
    public function __construct()
    {
        $this->productCategoryService = new ProductCategoryService();
    }
    public function productCategoryService()
    {
        return app(ProductCategoryService::class);
    }
    public function index(Request $request)
    {
        $data       = $request->all();
        $datatable  = $this->productCategoryService->filterDataTable($data);
        $categories = $this->productCategoryService->getAll();
        return Inertia::render('Admin/Product/Category/Index', [
            'data'       => $datatable,
            'filters'    => $request->only('search'),
            'categories' => $categories,
        ]);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $this->productCategoryService()->create($validated);
        return redirect()->back();
    }
    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $this->productCategoryService()->update($id, $validated);
        return redirect()->back();
    }
    public function destroy(int $id)
    {
        $this->productCategoryService()->delete($id);
        return redirect()->back();
    }
}
