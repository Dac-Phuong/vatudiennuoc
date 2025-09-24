<?php
namespace App\Services;

use App\Helpers\AdminLogs;
use App\Models\Setting;
use App\Traits\ThrowsValidationException;
use Illuminate\Support\Facades\Log;

class SettingService extends BaseService
{
    use ThrowsValidationException;
    protected function setModel()
    {
        $this->model = new Setting();
    }
    public function updateSetting(array $data): bool
    {
        try {
            foreach ($data as $key => $value) {
                $this->model->updateOrCreate(
                    ['key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }
            AdminLogs::store("Đã cập nhật <strong>cài đặt</strong>", auth()->id(), request()->ip());
            return true;
        } catch (\Throwable $th) {
            Log::error('Update setting failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
                'data'    => $data,
            ]);
            return false;
        }
    }

}
