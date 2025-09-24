<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\AdminLogs;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Traits\ThrowsValidationException;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    use ThrowsValidationException;
    public function index()
    {
        return Inertia::render('Admin/Auth/Login');
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->locked === 1) {
                Auth::logout();
                $this->throwValidation("Tài khoản của bạn đã bị khóa.");
            }
            if ((int) $user->role < 1) {
                Auth::logout();
                $this->throwValidation("Bạn không có quyền truy cập.");
            }
            // Log the admin login action
            AdminLogs::store('<p>Đăng nhập thành công vào <strong>hệ thống</strong>.</p>', $user->id, $request->ip());
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        $this->throwValidation("Mật khẩu không chính xác.");
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

}
