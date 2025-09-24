<?php
namespace App\Http\Requests\Admin;

use App\Traits\ValidateRequestException;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'    => [
                'required',
                'string',
                'email',
                'max:100',
                'exists:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Vui lòng nhập địa chỉ email.',
            'email.email'       => 'Địa chỉ email không đúng định dạng.',
            'email.max'         => 'Địa chỉ email không được vượt quá 100 ký tự.',
            'email.exists'      => 'Địa chỉ email không tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];
    }
}
