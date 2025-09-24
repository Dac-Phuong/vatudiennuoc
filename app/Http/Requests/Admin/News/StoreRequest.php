<?php
namespace App\Http\Requests\Admin\News;

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
            'title'             => 'required|string|max:255|unique:news,title',
            'slug'              => 'required|string|max:255|unique:news,slug',
            'thumbnail'         => 'nullable|image',
            'short_description' => 'nullable|string|max:255',
            'content'           => 'nullable|string',
            'is_show'           => 'required|boolean',
            'is_pin'            => 'required|boolean',
            'category_id'       => 'required|exists:news_categories,id',
            'author_id'         => 'nullable|exists:users,id',
            'tags'              => 'required|array',
        ];
    }
    public function messages(): array
    {
        return [
            'title.required'        => 'Tiêu đề là bắt buộc.',
            'title.unique'          => 'Tiêu đề đã tồn tại.',
            'slug.required'         => 'Slug là bắt buộc.',
            'slug.unique'           => 'Slug đã tồn tại.',
            'thumbnail.image'       => 'Thumbnail phải là hình ảnh.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 255 ký tự.',
            'content.required'      => 'Nội dung là bắt buộc.',
            'is_show.required'      => 'Trạng thái hiển thị là bắt buộc.',
            'is_pin.required'       => 'Trạng thái ghim là bắt buộc.',
            'category_id.required'  => 'Danh mục tin tức là bắt buộc.',
            'category_id.exists'    => 'Danh mục tin tức không tồn tại.',
            'author_id.required'    => 'Tác giả là bắt buộc.',
            'author_id.exists'      => 'Tác giả không tồn tại.',
            'tags.array'            => 'Thẻ phải là một mảng.',
        ];
    }
}
