<?php
namespace App\Http\Requests\Admin\ProductBrand;

use App\Traits\ValidateRequestException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name'    => 'required|string|max:255',
            'logo'    => 'nullable|image|max:2048',
            'slug'    => 'required|string|max:255|unique:product_brands,slug,' . $this->route('id'),
            'is_show' => 'nullable',
            'is_pin'  => 'nullable',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Tên thương hiệu không được để trống',
            'name.string'   => 'Tên thương hiệu phải là chuỗi ký tự',
            'name.max'      => 'Tên thương hiệu không được vượt quá 255 ký tự',
            'slug.required' => 'Slug không được để trống',
            'slug.string'   => 'Slug phải là chuỗi ký tự',
            'slug.max'      => 'Slug không được vượt quá 255 ký tự',
            'slug.unique'   => 'Slug đã tồn tại',
            'logo.image'    => 'Logo phải là file ảnh',
            'logo.max'      => 'Logo không được vượt quá 2MB',
        ];
    }
}
