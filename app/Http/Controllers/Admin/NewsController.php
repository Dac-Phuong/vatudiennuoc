<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\News\StoreRequest;
use App\Http\Requests\Admin\News\UpdateRequest;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public $newsService;
    public function __construct()
    {
        $this->newsService = new NewsService();
    }
    public function newsService()
    {
        return app(NewsService::class);
    }
    public function index(Request $request)
    {
        $data       = $request->all();
        $categories = $this->newsService()->getCategory();
        $news       = $this->newsService()->filterDataTable($data);
        return Inertia::render('Admin/News/Index', [
            'data'       => $news,
            'filters'    => $request->only('search'),
            'categories' => $categories,
        ]);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $this->newsService()->create($validated);
        return redirect()->back();
    }
    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $this->newsService()->update($id, $validated);
        return redirect()->back();
    }
    public function destroy(int $id)
    {
        $this->newsService()->delete($id);
        return redirect()->back();
    }

    public function getContent(Request $request, int $id)
    {
        $content = $this->newsService()->getContent($id);
        return response()->json([
            'content' => $content,
        ]);
    }
    public function updateContent(Request $request, int $id)
    {
        $content = $request->input('content');
        $this->newsService()->updateContent($id, $content);
        return redirect()->back();
    }
    public function getTags(Request $request, int $id)
    {
        $tags = $this->newsService()->getTags($id);
        return response()->json([
            'tags' => $tags,
        ]);
    }
}
