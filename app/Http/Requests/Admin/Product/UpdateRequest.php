<?php
namespace App\Http\Requests\Admin\Product;

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
            'id'               => 'required',
            'name'             => 'required|string|max:255',
            'slug'             => 'required|string|max:255|unique:products,slug,' . $this->route('id'),
            'price'            => 'required|numeric|min:0',
            'quantity'         => 'required|numeric|min:0',
            'category_id'      => 'required|exists:product_categories,id',
            'type'             => 'required|in:1,2,3,4',
            'thumbnail'        => 'nullable|image|max:2048',
            'images'           => 'nullable|array',
            'brand_id'         => 'nullable',
            'discount'         => 'nullable|numeric|min:0',
            'short_desc'       => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'seo'              => 'nullable|array',
        ];
    }
    public function messages(): array
    {
        return [
            'id.required'             => 'ID sản phẩm không được để trống',
            'name.required'           => 'Tên sản phẩm không được để trống',
            'name.string'             => 'Tên sản phẩm phải là chuỗi ký tự',
            'name.max'                => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'slug.required'           => 'Slug không được để trống',
            'slug.string'             => 'Slug phải là chuỗi ký tự',
            'slug.max'                => 'Slug không được vượt quá 255 ký tự',
            'slug.unique'             => 'Slug đã tồn tại',
            'short_desc.string'       => 'Mô tả ngắn phải là chuỗi ký tự',
            'short_desc.max'          => 'Mô tả ngắn không được vượt quá 500 ký tự',
            'content.string'          => 'Nội dung phải là chuỗi ký tự',
            'price.required'          => 'Giá sản phẩm không được để trống',
            'price.numeric'           => 'Giá sản phẩm phải là số',
            'price.min'               => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',
            'discount.numeric'        => 'Giảm giá phải là số',
            'discount.min'            => 'Giảm giá phải lớn hơn hoặc bằng 0',
            'thumbnail.image'         => 'Ảnh đại diện phải là file ảnh',
            'thumbnail.max'           => 'Kích thước ảnh đại diện không được vượt quá 2MB',
            'images.array'            => 'Dữ liệu ảnh không hợp lệ',
            'category_id.required'    => 'Danh mục sản phẩm không được để trống',
            'category_id.exists'      => 'Danh mục sản phẩm không tồn tại',
            'type.required'           => 'Loại sản phẩm không được để trống',
            'type.in'                 => 'Loại sản phẩm không hợp lệ',
            'meta_title.string'       => 'Tiêu đề SEO phải là chuỗi ký tự',
            'meta_title.max'          => 'Tiêu đề SEO không được vượt quá 255 ký tự',
            'meta_description.string' => 'Mô tả SEO phải là chuỗi ký tự',
            'meta_description.max'    => 'Mô tả SEO không được vượt quá 500 ký tự',
            'meta_keywords.string'    => 'Từ khóa SEO phải là chuỗi ký tự',
            'meta_keywords.max'       => 'Từ khóa SEO không được vượt quá 255 ký tự',
            'seo.array'               => 'Dữ liệu SEO không hợp lệ',
        ];
    }
}
