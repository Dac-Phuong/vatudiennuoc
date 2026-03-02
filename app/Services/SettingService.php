<?php
namespace App\Services;

use App\Models\Setting;
use App\Traits\ThrowsValidationException;

class SettingService extends BaseService
{
    use ThrowsValidationException;
    protected function setModel()
    {
        $this->model = new Setting();
    }
    public function updateSetting($request): bool
    {
        try {
            foreach ($request->all() as $key => $value) {

                if (in_array($key, ['_token', '_method'])) {
                    continue;
                }

                if ($request->hasFile($key)) {
                    $file = $request->file($key);

                    if ($file && $file->isValid()) {
                        $path = parent::uploadImage($file);
                        Setting::updateOption($key, $path);
                    }
                } else {
                    Setting::updateOption($key, $value);
                }
            }

            return true;

        } catch (\Throwable $th) {
            $this->throwValidation("Lỗi khi lưu cấu hình: " . $th->getMessage());
            return false; 
        }
    }

}
