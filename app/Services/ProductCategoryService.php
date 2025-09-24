<?php
namespace App\Services;

use App\Helpers\AdminLogs;
use App\Models\ProductCategory;
use App\Traits\ThrowsValidationException;

class ProductCategoryService extends BaseService
{
    use ThrowsValidationException;
    public function setModel()
    {
        $this->model = new ProductCategory();
    }
    public function filterDataTable(array $data)
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

            $direction = $sortOrder == 1 ? 'asc' : 'desc';

            // Chỉ cho phép sort theo các field hợp lệ (tránh SQL injection)
            $allowedSortFields = ['id', 'slug', 'name', 'created_at'];
            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            // Query
            $result = $query->select('id', 'name', 'slug', 'is_show', 'created_at')
                ->with('parent')
                ->orderBy($sortField, $direction)
                ->paginate($per_page);

            if ($search) {
                $result->appends(['search' => $search]);
            }

            return $result;
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi lọc dữ liệu: " . $th->getMessage());
        }
    }
    public function create(array $data)
    {
        try {
            if ($data['parent_id']) {
                $data['parent_id'] = (int) $data['parent_id'];
            }
            parent::create($data);
            $name = e($data['name']);
            AdminLogs::store("Thêm danh mục <strong>{$name}</strong>", auth()->id(), request()->ip());
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi thêm danh mục: " . $th->getMessage());
        }
    }
    public function update(int $id, array $data)
    {
        try {
            $category = $this->findById($id);
            if (! $category) {
                $this->throwValidation("Danh mục không tồn tại.");
            }

            if (isset($data['parent_id']) && $data['parent_id'] && $category->id == $data['parent_id']) {
                $data['parent_id'] = null;
            }

            parent::update($id, $data);
            $name = e($data['name']);
            AdminLogs::store("Cập nhật danh mục <strong>{$name}</strong>", auth()->id(), request()->ip());
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi cập nhật danh mục: " . $th->getMessage());
        }
    }
    public function delete(int $id)
    {
        try {
            $category = $this->findById($id);
            if (! $category) {
                $this->throwValidation("Danh mục không tồn tại.");
            }
            parent::delete($id);
            $name = e($category->name);
            AdminLogs::store("Xoá danh mục <strong>{$name}</strong>", auth()->id(), request()->ip());
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi xoá danh mục: " . $th->getMessage());
        }
    }

}
