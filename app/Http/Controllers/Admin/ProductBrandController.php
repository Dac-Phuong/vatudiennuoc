<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductBrand\StoreRequest;
use App\Http\Requests\Admin\ProductBrand\UpdateRequest;
use App\Services\ProductBrandService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductBrandController extends Controller
{
    public $productBrandService;
    public function __construct()
    {
        $this->productBrandService = new ProductBrandService();
    }
    public function productBrandService()
    {
        return app(ProductBrandService::class);
    }
    public function index(Request $request)
    {
        $data       = $request->all();
        $datatable  = $this->productBrandService->filterDataTable($data);
        $categories = $this->productBrandService->getAll();
        return Inertia::render('Admin/Product/Brand/Index', [
            'data'       => $datatable,
            'filters'    => $request->only('search'),
            'categories' => $categories,
        ]);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $this->productBrandService()->create($validated);
        return redirect()->back();
    }
    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $this->productBrandService()->update($id, $validated);
        return redirect()->back();
    }
    public function destroy(int $id)
    {
        $this->productBrandService()->delete($id);
        return redirect()->back();
    }
}
