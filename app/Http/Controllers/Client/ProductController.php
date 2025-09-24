<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\SearchProduct;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{

    public $products;
    public $categories;
    public function __construct()
    {
        $this->products   = new ProductService();
        $this->categories = new ProductCategoryService();
    }
    public function index(Request $request)
    {
        $data     = $request->all();
        $products = $this->products->getProductFilter($data);
        return Inertia::render('Client/Products/Index', [
            'products'   => $products,
            'categories' => $this->products->getCategory(),
            'brands'     => $this->products->getBrand(),
            'filters'    => $data,
        ]);
    }
    public function detail($slug)
    {
        $relatedProducts = $this->products->getRelatedProducts($slug);
        $product         = $this->products->findBySlug($slug);
        return Inertia::render('Client/Products/Detail', [
            'product'         => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
    public function productCategory(Request $request, $slug)
    {
        $data     = $request->all();
        $products = $this->products->getProductCategoryFilter($data, $slug);
        $category = $this->categories->findBySlug($slug);
        return Inertia::render('Client/Products/Category', [
            'products'   => $products,
            'categories' => $this->products->getCategory(),
            'filters'    => $data,
            'category'   => $category,
        ]);
    }
    public function search(SearchProduct $request)
    {
        try {
            $data     = $request->all();
            $products = $this->products->getProductSearch($data);
            return response()->json([
                'status'  => true,
                'message' => 'Lấy danh sách sản phẩm thành công',
                'data'    => $products,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi: ' . $th->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

}
