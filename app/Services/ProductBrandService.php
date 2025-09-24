<?php
namespace App\Services;

use App\Helpers\AdminLogs;
use App\Models\ProductBrand;
use App\Traits\ThrowsValidationException;

class ProductBrandService extends BaseService
{
    use ThrowsValidationException;
    public function setModel()
    {
        $this->model = new ProductBrand();
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
            $result = $query->select('id', 'name', 'slug', 'logo', 'is_show', 'is_pin', 'created_at')
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
            if (! empty($data['logo'])) {
                $data['logo'] = $this->uploadImage($data['logo'], 'brands');
            } else {
                unset($data['logo']);
            }
            parent::create($data);
            $name = e($data['name']);
            AdminLogs::store("Thêm thương hiệu <strong>{$name}</strong>", auth()->id(), request()->ip());
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi thêm thương hiệu: " . $th->getMessage());
        }
    }
    public function update(int $id, array $data)
    {
        try {
            if (! empty($data['logo'])) {
                $data['logo'] = $this->uploadImage($data['logo'], 'brands');
            } else {
                unset($data['logo']);
            }
            $brand = $this->findById($id);
            if (! $brand) {
                $this->throwValidation("Thương hiệu không tồn tại.");
            }

            parent::update($id, $data);
            $name = e($data['name']);
            AdminLogs::store("Cập nhật thương hiệu <strong>{$name}</strong>", auth()->id(), request()->ip());
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi cập nhật thương hiệu: " . $th->getMessage());
        }
    }
    public function delete(int $id)
    {
        try {
            $category = $this->findById($id);
            if (! $category) {
                $this->throwValidation("thương hiệu không tồn tại.");
            }
            parent::delete($id);
            $name = e($category->name);
            AdminLogs::store("Xoá thương hiệu <strong>{$name}</strong>", auth()->id(), request()->ip());
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi xoá thương hiệu: " . $th->getMessage());
        }
    }

}
