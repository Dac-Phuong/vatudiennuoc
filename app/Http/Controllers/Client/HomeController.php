<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use App\Services\ProductService;
use Inertia\Inertia;

class HomeController extends Controller
{
    public $products;
    public $newsService;
    public function __construct()
    {
        $this->products    = new ProductService();
        $this->newsService = new NewsService();
    }
    public function index()
    {
        $productSale        = $this->products->productSale();
        $ProductBestSelling = $this->products->ProductBestSelling();
        $productBrands      = $this->products->productBrand();
        $firstNews          = $this->newsService->firstNews();
        $news               = $this->newsService->getNewHome();
        return Inertia::render('Client/Home/Index', [
            'productSale'        => $productSale,
            'ProductBestSelling' => $ProductBestSelling,
            'productBrands'      => $productBrands,
            'firstNews'          => $firstNews,
            'news'               => $news,
        ]);
    }

}
