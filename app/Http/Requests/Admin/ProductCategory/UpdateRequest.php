<?php
namespace App\Http\Requests\Admin\ProductCategory;

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
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:product_categories,slug,' . $this->route('id'),
            'is_show'   => 'nullable',
            'parent_id' => 'nullable|exists:product_categories,id',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'    => 'Tên danh mục không được để trống',
            'name.string'      => 'Tên danh mục phải là chuỗi ký tự',
            'name.max'         => 'Tên danh mục không được vượt quá 255 ký tự',
            'slug.required'    => 'Slug không được để trống',
            'slug.string'      => 'Slug phải là chuỗi ký tự',
            'slug.max'         => 'Slug không được vượt quá 255 ký tự',
            'slug.unique'      => 'Slug đã tồn tại',
            'parent_id.exists' => 'Danh mục cha không tồn tại',
        ];
    }
}
