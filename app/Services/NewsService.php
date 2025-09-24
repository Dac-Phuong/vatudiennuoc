<?php
namespace App\Services;

use App\Helpers\AdminLogs;
use App\Helpers\DeleteFile;
use App\Models\News;
use App\Models\Tags;
use App\Traits\ThrowsValidationException;
use Illuminate\Support\Facades\DB;

class NewsService extends BaseService
{
    use ThrowsValidationException;
    public function setModel()
    {
        $this->model = new News();
    }

    public function filterDataTable($data)
    {
        try {
            $search    = $data['search'] ?? null;
            $per_page  = (int) ($data['per_page'] ?? 10);
            $sortField = $data['sortField'] ?? 'created_at';
            $sortOrder = (int) ($data['sortOrder'] ?? -1);

            $query = $this->model->query();

            // Tìm kiếm
            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }

            // Chỉ cho phép sort theo các field hợp lệ
            $allowedSortFields = ['id', 'views', 'is_show', 'title', 'is_pin', 'created_at'];
            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            $direction = $sortOrder === 1 ? 'asc' : 'desc';

            // Query
            $result = $query->select([
                'id', 'title', 'views', 'is_show', 'is_pin', 'thumbnail',
                'category_id', 'slug', 'short_description', 'author_id', 'created_at',
            ])
                ->with(['category:id,name', 'author:id,full_name', 'tags:id,name'])
                ->orderBy($sortField, $direction)
                ->paginate($per_page)
                ->appends($data);

            return $result;
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi lọc dữ liệu: " . $th->getMessage());
        }
    }

    public function create(array $data)
    {
        $data['author_id'] = auth()->user()->id;
        try {

            DB::beginTransaction();
            if ($data['thumbnail']) {
                $data['thumbnail'] = $this->uploadImage($data['thumbnail'], 'news');
            }
            $news = parent::create($data);
            // Sync tags
            if (! empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag      = Tags::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $news->tags()->sync($tagIds);
            }
            // Lưu log
            AdminLogs::store("Thêm mới tin tức <strong>{$news->title}</strong>", auth()->id(), request()->ip());
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->throwValidation($th->getMessage());
            return false;
        }
    }

    public function update(int $id, array $data)
    {
        $news = $this->model->find($id);
        if (! $news) {
            $this->throwValidation("Tin tức không tồn tại.");
        }
        try {
            DB::beginTransaction();
            if ($data['thumbnail']) {
                $data['thumbnail'] = $this->uploadImage($data['thumbnail'], 'news');
                DeleteFile::delete($news->thumbnail);
            } else {
                unset($data['thumbnail']);
            }
            parent::update($id, $data);
            // Sync tags
            if (! empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag      = Tags::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $news->tags()->sync($tagIds);
            }
            AdminLogs::store("Cập nhật tin tức <strong>{$data['title']}</strong>");
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->throwValidation($th->getMessage());
            return false;
        }
    }

    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $news = News::find($id);
            if (! $news) {
                $this->throwValidation("Tin tức không tồn tại.");
            }
            // Xóa file nếu tồn tại
            DeleteFile::delete($news->thumbnail);
            // xóa bản ghi
            parent::delete($id);
            // lưu log
            AdminLogs::store("Xóa tin tức <strong>{$news->title}</strong>");
            DB::commit();
            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->throwValidation("Xóa tin tức thất bại: " . $th->getMessage());
            return false;
        }
    }
    public function getContent(int $id)
    {
        return $this->model->find($id)->content;
    }
    public function updateContent(int $id, string $content)
    {
        $news = $this->model->find($id);
        if (! $news) {
            $this->throwValidation("Tin tức không tồn tại.");
        }
        try {
            DB::beginTransaction();
            $news->content = $content;
            $news->save();
            AdminLogs::store("Cập nhật nội dung tin tức <strong>{$news->title}</strong>");
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->throwValidation("Cập nhật nội dung thất bại: " . $th->getMessage());
            return false;
        }
    }
    public function getTags(int $id)
    {
        $news = $this->model->find($id);
        if (! $news) {
            $this->throwValidation("Tin tức không tồn tại.");
        }
        return $news->tags;
    }
    public function getNewsByFilter($data)
    {
        $pageLength = 6;
        $pageNumber = $data['page'] ?? 1;
        $skip       = ($pageNumber - 1) * $pageLength;

        $query = News::query();
        if (isset($data['category_slug']) && $data['category_slug'] != 'all') {
            $query->whereHas('category', function ($q) use ($data) {
                $q->where('slug', $data['category_slug']);
            });
        }

        if (isset($data['search'])) {
            $query->where('title', 'like', "%" . $data['search'] . "%");
        }

        $query->where('is_show', News::STATUS_ACTIVE)
            ->select('id', 'title', 'slug', 'views', 'short_description', 'author_id', 'created_at', 'new_category_id', 'thumbnail')
            ->with([
                'category:id,name',
            ]);

        $recordsTotal = $query->count();
        $result       = $query
            ->orderBy('is_pin', News::STATUS_PIN ? 'DESC' : 'ASC')
            ->orderByDesc('id')
            ->skip($skip)
            ->take($pageLength)
            ->get();
        $pageCount = ceil($recordsTotal / $pageLength);

        return [
            'result'     => $result,
            'page_count' => $pageCount,
        ];
    }

    public function findBySlug(string $slug)
    {
        $news = $this->model->where('slug', $slug)->with(['author:id,full_name', 'category:id,name,slug', 'tags'])->first();
        if ($news) {
            $news->views = $news->views + 1;
            $news->save();
        }
        return $news;
    }
    public function getRelatedBySlug(string $slug, int $limit = 5)
    {
        $news = $this->model->where('slug', $slug)->first();

        if (! $news) {
            return collect([]);
        }

        return $this->model->with('author')
            ->where('category_id', $news->category_id)
            ->where('id', '<>', $news->id)
            ->latest()
            ->take($limit)
            ->get();
    }
    public function featuredNews(int $limit = 5)
    {
        $news = $this->model->where('is_pin', 1)->take($limit)->get();
        return $news;
    }

    public function getAllNews(array $data)
    {
        try {
            $query = $this->model->newQuery()->with(['author', 'tags', 'category']);

            // Filter theo category
            if (! empty($data['category']) && $data['category'] !== 'all') {
                $query->where('category', $data['category']);
            }

            return $query->orderBy('created_at', 'desc')
                ->paginate(9)
                ->withQueryString();

        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
        }
    }
    public function getNewHome()
    {
        try {
            return $this->model
                ->select('id', 'thumbnail', 'title', 'short_description', 'slug', 'created_at', 'author_id')
                ->with('author:id,full_name')
                ->where('is_pin', 1)
                ->limit(10)
                ->get();
        } catch (\Throwable $th) {
            \Log::error('Error' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
            ]);
            return collect();
        }
    }
    public function firstNews()
    {
        try {
            return $this->model
                ->select('id', 'thumbnail', 'title', 'short_description', 'slug', 'created_at', 'author_id')
                ->with('author:id,full_name')
                ->where('is_pin', 1)
                ->latest()
                ->first();
        } catch (\Throwable $th) {
            \Log::error('Error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
            ]);
            return null; // đồng bộ kiểu dữ liệu với first()
        }
    }

    public function getCategory()
    {
        return $this->newsCategoryService()->getAll();
    }
    public function newsCategoryService()
    {
        return app(NewsCategoryService::class);
    }
}
