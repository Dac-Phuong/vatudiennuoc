<?php
namespace App\Http\Requests\Admin\UploadFile;

use App\Traits\ValidateRequestException;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    use ValidateRequestException;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'images.required' => 'Vui lòng chọn file để tải lên',
            'images.array'    => 'Dữ liệu tải lên không hợp lệ',
            'images.*.image'  => 'File tải lên phải là ảnh',
            'images.*.mimes'  => 'File tải lên phải có định dạng: jpg, jpeg, png, gif',
            'images.*.max'    => 'Kích thước file tải lên không được vượt quá 2MB',
        ];
    }
}
