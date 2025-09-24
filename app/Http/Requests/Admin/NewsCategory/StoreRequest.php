<?php
namespace App\Http\Requests\Admin\NewsCategory;

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
            'name'    => 'required|string|max:255',
            'slug'    => 'required|string|max:255|unique:news_categories,slug',
            'is_show' => 'required|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'    => 'Tên danh mục là bắt buộc.',
            'name.max'         => 'Tên danh mục không được vượt quá 255 ký tự.',
            'slug.required'    => 'Slug là bắt buộc.',
            'slug.max'         => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique'      => 'Slug đã tồn tại.',
            'is_show.required' => 'Trạng thái hiển thị là bắt buộc.',
        ];
    }
}
