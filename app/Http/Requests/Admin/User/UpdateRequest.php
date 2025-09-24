<?php
namespace App\Http\Requests\Admin\User;

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
            'id'        => 'required|numeric|exists:users,id',
            'full_name' => 'nullable|string|max:255',
            'email'     => 'nullable|email|max:255',
            'role'      => 'nullable|numeric|in:0,1,100',
            'balance'   => 'nullable|numeric|min:0',
            'locked'    => 'nullable|boolean',
            'password'  => 'nullable|string|min:6|max:12',
            'phone'     => 'nullable|string|max:12',
        ];
    }
    public function messages(): array
    {
        return [
            'id.required'     => 'ID là bắt buộc.',
            'id.numeric'      => 'ID phải là một số.',
            'id.exists'       => 'ID không tồn tại.',
            'e.max'           => 'Tài khoản không được vượt quá :max ký tự.',
            'email.email'     => 'Email không hợp lệ.',
            'role.in'         => 'Vai trò không hợp lệ.',
            'balance.min'     => 'Số dư phải lớn hơn hoặc bằng :min.',
            'phone.max'       => 'Số điện thoại không được vượt quá :max ký tự.',
            'role.numeric'    => 'Vai trò phải là một số.',
            'balance.numeric' => 'Số dư phải là một số.',
            'password.min'    => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.max'    => 'Mật khẩu không được vượt quá :max ký tự.',
        ];
    }

}
