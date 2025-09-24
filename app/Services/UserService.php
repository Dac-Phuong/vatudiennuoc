<?php
namespace App\Services;

use App\Helpers\AdminLogs;
use App\Models\User;
use App\Traits\ThrowsValidationException;
use Hash;

class UserService extends BaseService
{
    use ThrowsValidationException;
    protected function setModel()
    {
        $this->model = new User();
    }
    public function filterDataTable(array $data)
    {
        try {
            $search    = $data['search'] ?? null;
            $per_page  = $data['per_page'] ?? 10;
            $sortField = $data['sortField'] ?? 'created_at';
            $sortOrder = $data['sortOrder'] ?? -1;

            $query = $this->model->query();

            // Tìm kiếm
            if ($search) {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            }

            $direction = $sortOrder == 1 ? 'asc' : 'desc';
            // Chỉ cho phép sort theo các field hợp lệ (tránh SQL injection)
            $allowedSortFields = ['id', 'full_name', 'role', 'email', 'balance', 'locked', 'phone', 'created_at'];
            if (! in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            // Query
            $users = $query->select('id', 'full_name', 'role', 'email', 'balance', 'locked', 'phone', 'created_at')
                ->orderBy($sortField, $direction)
                ->paginate($per_page);

            if ($search) {
                $users->appends(['search' => $search]);
            }

            return $users;
        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi lọc dữ liệu: " . $th->getMessage());
        }
    }

    public function update(int $id, array $data)
    {
        $user = $this->findById($id);

        if (! $user) {
            $this->throwValidation("Người dùng với ID {$id} không tồn tại.");
        }
        try {
            // kiểm tra quyền
            if (auth()->user()->role !== 100 && isset($data['role'])) {
                unset($data['role']);
            }
            if (auth()->user()->role !== 100 && isset($data['locked'])) {
                unset($data['locked']);
            }
            if ($user->role == 100 && isset($data['locked']) && auth()->user()->role !== 100) {
                $this->throwValidation("không thể khóa tài khoản này");
            }
            // kiểm tra và mã hóa mật khẩu
            if ($data['password'] ?? false) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            // ghi log
            $content = "<p>Đã cập nhật thông tin người dùng <strong>{$user->account}</strong>.</p>";
            AdminLogs::store($content);
            // cập nhật thông tin người dùng
            parent::update($id, $data);
            return true;
        } catch (\Throwable $th) {
            $this->throwValidation("Cập nhật thất bại: " . $th->getMessage());
        }
    }
    public function delete(int $id)
    {
        $user = $this->findById($id);
        if (! $user) {
            $this->throwValidation("Người dùng với ID {$id} không tồn tại.", 'id');
        }
        try {
            if ($user->id === auth()->id()) {
                $this->throwValidation("không thể xóa chính bạn.");
            }
            // kiểm tra quyền
            if (auth()->user()->role !== 100) {
                $this->throwValidation("Bạn không có quyền xóa người dùng này.");
            }
            // ghi log
            $content = "<p>Đã xóa người dùng <strong>{$user->account}</strong>.</p>";
            AdminLogs::store($content);
            // xóa người dùng
            parent::delete($id);
            return true;
        } catch (\Throwable $th) {
            $this->throwValidation("Xóa người dùng thất bại: " . $th->getMessage());
        }
    }
}
