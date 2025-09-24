<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public $productService;
    public function __construct()
    {
        $this->productService = new ProductService();
    }
    public function productService()
    {
        return app(ProductService::class);
    }
    public function index(Request $request)
    {
        $data     = $request->all();
        $products = $this->productService()->filterDataTable($data);
        return Inertia::render('Admin/Product/Index', [
            'data'    => $products,
            'filters' => $request->only('search'),
        ]);
    }
    public function create()
    {
        $categories = $this->productService()->getCategory();
        $brands     = $this->productService()->getBrand();
        return Inertia::render('Admin/Product/Create', [
            'categories' => $categories,
            'brands'     => $brands,
        ]);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $this->productService()->create($validated);
        return redirect()->back();
    }
    public function edit(int $id)
    {
        $product    = $this->productService()->findById($id);
        $categories = $this->productService()->getCategory();
        $brands     = $this->productService()->getBrand();
        return Inertia::render('Admin/Product/Update', [
            'product'    => $product,
            'categories' => $categories,
            'brands'     => $brands,
        ]);
    }
    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $this->productService()->update($id, $validated);
        return redirect()->back();
    }
    public function destroy(int $id)
    {
        $this->productService()->delete($id);
        return redirect()->back();
    }
}
