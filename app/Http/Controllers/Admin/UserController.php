<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public $userService;
    public function __construct()
    {
        $this->userService = new UserService();
    }
    public function userService()
    {
        return app(UserService::class);
    }
    public function index(Request $request)
    {
        $data  = $request->all();
        $users = $this->userService()->filterDataTable($data);
        return Inertia::render('Admin/User/Index', [
            'users'   => $users,
            'filters' => $request->only('search'),
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $data = $request->validated();
        $this->userService()->update($data['id'], $data);
        return redirect()->back();

    }
    public function destroy(int $id)
    {
        $this->userService()->delete($id);
        return redirect()->back();
    }

}
