<?php
namespace App\Http\Requests\Client;

use App\Traits\ValidateRequestException;
use Illuminate\Foundation\Http\FormRequest;

class SearchProduct extends FormRequest
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
            'search' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\s]+$/u', // Cho phép chữ cái (có dấu), số và khoảng trắng
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' => 'Vui lòng nhập từ khóa tìm kiếm.',
            'search.string'   => 'Từ khóa phải là một chuỗi ký tự.',
            'search.max'      => 'Từ khóa không được vượt quá 255 ký tự.',
            'search.regex'    => 'Từ khóa chỉ được phép chứa chữ cái, số và khoảng trắng (không chứa ký tự đặc biệt).',
        ];
    }

}
