<?php
namespace App\Services;

use App\Helpers\AdminLogs;
use App\Helpers\DeleteFile;
use App\Models\News;
use App\Models\ProductBrand;
use App\Models\ProductImage;
use App\Models\Products;
use App\Traits\ThrowsValidationException;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{
    use ThrowsValidationException;
    public function setModel()
    {
        $this->model = new Products();
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
                $query->where('name', 'like', "%{$search}%");
            }

            // Chỉ cho phép sort theo các field hợp lệ
            $allowedSortFields = ['id', 'name', 'created_at'];
            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            $direction = $sortOrder === 1 ? 'asc' : 'desc';

            // Query
            $result = $query->select([
                'id', 'name', 'price', 'thumbnail', 'discount',
                'category_id', 'slug', 'type', 'quantity', 'brand_id', 'status', 'created_at',
            ])
                ->with(['category:id,name', 'brand:id,name'])
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
        try {
            DB::beginTransaction();
            if (! empty($data['thumbnail'])) {
                $data['thumbnail'] = $this->uploadImage($data['thumbnail'], 'products');
            }
            $images = $data['images'] ?? [];
            $seo    = $data['seo'] ?? [];
            // Tạo sản phẩm
            $product = parent::create($data);
            if ($product && ! empty($images)) {
                foreach ($images as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $path = $this->uploadImage($image, 'products');
                    } else {
                        $path = $image['url'] ?? null;
                    }

                    if ($path) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                        ]);
                    }
                }
            }
            if (! empty($seo)) {
                $product->seo()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'meta_title'       => $seo['meta_title'] ?? null,
                        'meta_description' => $seo['meta_description'] ?? null,
                        'meta_keywords'    => $seo['meta_keywords'] ?? null,
                    ]
                );
            }
            // Lưu log
            AdminLogs::store("Thêm sản phẩm mới <strong>{$product->name}</strong>", auth()->id(), request()->ip());

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
        $product = $this->model->find($id);
        if (! $product) {
            $this->throwValidation("Sản phẩm không tồn tại.");
        }
        try {
            DB::beginTransaction();
            // Nếu có ảnh đại diện mới thì upload và gán lại
            if (! empty($data['thumbnail'])) {
                $data['thumbnail'] = $this->uploadImage($data['thumbnail'], 'products');
            } else {
                unset($data['thumbnail']);
            }
            $images = $data['images'] ?? [];
            $seo    = $data['seo'] ?? [];
            // Cập nhật thông tin sản phẩm
            parent::update($id, $data);
            // Nếu có hình ảnh mới, tiến hành lưu
            if (! empty($images) && is_array($images)) {
                foreach ($images as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $path = $this->uploadImage($image, 'products');
                    } else {
                        $path = $image['url'] ?? null;
                    }

                    if ($path) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                        ]);
                    }
                }
            }
            if (! empty($seo)) {
                $product->seo()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'meta_title'       => $seo['meta_title'] ?? null,
                        'meta_description' => $seo['meta_description'] ?? null,
                        'meta_keywords'    => $seo['meta_keywords'] ?? null,
                    ]
                );
            }
            // Ghi lại log quản trị
            AdminLogs::store("Cập nhật sản phẩm <strong>{$data['name']}</strong>");

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->throwValidation("Lỗi khi cập nhật: " . $th->getMessage());
            return false;
        }
    }

    public function findById(int $id)
    {
        try {
            $product = $this->model
                ->with(['images', 'seo'])
                ->find($id);
            return $product;
        } catch (\Throwable $th) {
            $this->throwValidation($th->getMessage());
            return null;
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
    // client
    public function getProductFilter(array $filters)
    {
        try {
            $query = $this->model->query();
            // Lọc theo danh mục
            if (! empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            // Lọc theo thương hiệu (hỗ trợ mảng hoặc id)
            if (! empty($filters['brand_id'])) {
                if (is_array($filters['brand_id'])) {
                    $query->whereIn('brand_id', $filters['brand_id']);
                } else {
                    $query->where('brand_id', $filters['brand_id']);
                }
            }

            // Lọc theo khoảng giá
            if (! empty($filters['price_min'])) {
                $query->where('price', '>=', (int) $filters['price_min']);
            }
            if (! empty($filters['price_max'])) {
                $query->where('price', '<=', (int) $filters['price_max']);
            }

            // Tìm kiếm theo tên sản phẩm hoặc thương hiệu
            if (! empty($filters['search'])) {
                $searchTerm = $filters['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhereHas('brand', function ($q2) use ($searchTerm) {
                            $q2->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Sắp xếp
            $allowedSortFields = ['id', 'name', 'price', 'created_at', 'rating'];
            $sortField         = $filters['sortField'] ?? 'created_at';
            $sortOrder         = (int) ($filters['sortOrder'] ?? -1); // 1 = asc, -1 = desc
            $direction         = $sortOrder === 1 ? 'asc' : 'desc';

            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            // Phân trang
            $perPage = (int) ($filters['per_page'] ?? 12);

            $products = $query->select([
                'id', 'name', 'price', 'thumbnail',
                'category_id', 'slug', 'type', 'quantity',
                'brand_id', 'status', 'created_at',
            ])
                ->with(['category:id,name', 'brand:id,name', 'images'])
                ->orderBy($sortField, $direction)
                ->paginate($perPage)
                ->appends($filters);

            return $products;

        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi lọc sản phẩm: " . $th->getMessage());
            return null;
        }
    }
    public function findBySlug(string $slug)
    {
        try {
            $product = $this->model
                ->with(['category', 'brand', 'images', 'seo'])
                ->where('slug', $slug)
                ->first();
            if (! $product) {
                $this->throwValidation("Sản phẩm không tồn tại.");
            }
            return $product;
        } catch (\Throwable $th) {
            $this->throwValidation($th->getMessage());
            return null;
        }
    }
    public function getProductCategoryFilter(array $filters, string $slug)
    {
        try {
            $query = $this->model->query();
            // Lọc theo danh mục
            if (! empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            // Lọc theo thương hiệu (hỗ trợ mảng hoặc id)
            if (! empty($filters['brand_id'])) {
                if (is_array($filters['brand_id'])) {
                    $query->whereIn('brand_id', $filters['brand_id']);
                } else {
                    $query->where('brand_id', $filters['brand_id']);
                }
            }

            // Lọc theo khoảng giá
            if (! empty($filters['price_min'])) {
                $query->where('price', '>=', (int) $filters['price_min']);
            }
            if (! empty($filters['price_max'])) {
                $query->where('price', '<=', (int) $filters['price_max']);
            }

            // Tìm kiếm theo tên sản phẩm hoặc thương hiệu
            if (! empty($filters['search'])) {
                $searchTerm = $filters['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhereHas('brand', function ($q2) use ($searchTerm) {
                            $q2->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Sắp xếp
            $allowedSortFields = ['id', 'name', 'price', 'created_at', 'rating'];
            $sortField         = $filters['sortField'] ?? 'created_at';
            $sortOrder         = (int) ($filters['sortOrder'] ?? -1); // 1 = asc, -1 = desc
            $direction         = $sortOrder === 1 ? 'asc' : 'desc';

            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            // Phân trang
            $perPage = (int) ($filters['per_page'] ?? 12);

            $products = $query->select([
                'id', 'name', 'price', 'thumbnail',
                'category_id', 'slug', 'type', 'quantity',
                'brand_id', 'status', 'created_at',
            ])
                ->whereHas('category', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->with(['category:id,name', 'brand:id,name', 'images'])
                ->orderBy($sortField, $direction)
                ->paginate($perPage)
                ->appends($filters);

            return $products;

        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi lọc sản phẩm: " . $th->getMessage());
            return null;
        }
    }
    public function getRelatedProducts(string $slug, int $limit = 8)
    {
        try {
            $product = $this->model->where('slug', $slug)->first();
            if (! $product) {
                $this->throwValidation("Sản phẩm không tồn tại.");
            }
            $relatedProducts = $this->model
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->with(['category:id,name', 'brand:id,name', 'images'])
                ->limit($limit)
                ->get();
            return $relatedProducts;
        } catch (\Throwable $th) {
            $this->throwValidation($th->getMessage());
            return null;
        }
    }

    public function getProductSearch(array $data)
    {
        try {
            $query = $this->model->query();

            if (! empty($data['search'])) {
                $searchTerm = $data['search'];
                $query->where('name', 'like', "%{$searchTerm}%");
            }
            return $query->limit(12)->get() ?: collect();

        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi tìm kiếm sản phẩm: " . $th->getMessage());
            return collect();
        }
    }
    public function productSale()
    {
        try {
            $products = $this->model
                ->where('type', 4)
                ->with('images')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return $products;

        } catch (\Throwable $th) {
            \Log::error('Error fetching sale products: ' . $th->getMessage());
            return collect();
        }
    }
    public function ProductBestSelling()
    {
        try {
            $products = $this->model
                ->where('type', 3)
                ->with('images')
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();

            return $products;

        } catch (\Throwable $th) {
            \Log::error('Error fetching sale products: ' . $th->getMessage());
            return collect();
        }
    }
    public function productBrand()
    {
        try {
            $products = ProductBrand::where('is_pin', 1)
                ->with(['products.images'])
                ->first();

            return $products;
        } catch (\Throwable $th) {
            \Log::error('Error fetching sale products: ' . $th->getMessage());
            return collect();
        }
    }

    public function getCategory()
    {
        return $this->productCategoryService()->getAll();
    }
    public function getBrand()
    {
        return $this->productBrandService()->getAll();
    }
    public function productCategoryService()
    {
        return app(ProductCategoryService::class);
    }
    public function productBrandService()
    {
        return app(ProductBrandService::class);
    }
}
