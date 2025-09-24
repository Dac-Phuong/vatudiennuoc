<?php
namespace App\Services;

use App\Models\AdminLog;
use App\Traits\ThrowsValidationException;

class AdminLogService extends BaseService
{
    use ThrowsValidationException;
    public function setModel()
    {
        $this->model = new AdminLog();
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
                $query->where('content', 'like', "%{$search}%");
            }

            $direction = $sortOrder == 1 ? 'asc' : 'desc';

            // Chỉ cho phép sort theo các field hợp lệ (tránh SQL injection)
            $allowedSortFields = ['id', 'created_at'];
            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            // Query
            $result = $query->select('id', 'user_id', 'ip_address', 'content', 'created_at')
                ->with('user:id,account,role')
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

    public function delete(int $id)
    {
        try {
            $log = $this->findById($id);
            if (! $log) {
                $this->throwValidation("Lịch sử không tồn tại.");
            }
            if (auth()->user()->role !== 100) {
                $this->throwValidation("Bạn không có quyền xoá lịch sử này.");
            }
            parent::delete($id);
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi xoá lịch sử: " . $th->getMessage());
        }
    }

}
