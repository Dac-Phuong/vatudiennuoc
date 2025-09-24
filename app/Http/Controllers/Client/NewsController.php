<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\NewsCategoryService;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public $newsService;
    public $newsCategoryService;
    public function __construct()
    {
        $this->newsService         = new NewsService();
        $this->newsCategoryService = new NewsCategoryService();

    }
    public function index(Request $request)
    {
        $data         = $request->all();
        $newCategory  = $this->newsCategoryService->getCategories();
        $news         = $this->newsService->getAllNews($data);
        $featuredNews = $this->newsService->featuredNews();
        return Inertia::render('Client/News/Index', [
            'newCategory'  => $newCategory,
            'featuredNews' => $featuredNews,
            'news'         => $news,
        ]);
    }
    public function detail(string $slug)
    {
        $newCategory   = $this->newsCategoryService->getCategories();
        $news          = $this->newsService->findBySlug($slug);
        $relatedBySlug = $this->newsService->getRelatedBySlug($slug);
        return Inertia::render('Client/News/Detail', [
            'newCategory'   => $newCategory,
            'relatedBySlug' => $relatedBySlug,
            'news'          => $news,
        ]);
    }
    public function category(string $slug)
    {
        $newCategory  = $this->newsCategoryService->getCategories();
        $news         = $this->newsCategoryService->getNewCategory($slug);
        $featuredNews = $this->newsService->featuredNews();
        $category     = $this->newsCategoryService->findBySlug($slug);
        return Inertia::render('Client/News/Category', [
            'newCategory'  => $newCategory,
            'category'     => $category,
            'featuredNews' => $featuredNews,
            'news'         => $news,
        ]);
    }
}
