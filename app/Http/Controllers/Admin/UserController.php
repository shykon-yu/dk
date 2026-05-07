<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\Admin\DepartmentService;
use App\Services\Admin\RoleService;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->middleware('permission:admin.users.index')->only('index','show');
        $this->middleware('permission:admin.users.store')->only('create', 'store');
        $this->middleware('permission:admin.users.update')->only('status');
        $this->middleware('permission:admin.users.destroy')->only('destroy','batchDestroy');
        $this->middleware('permission:admin.users.reset')->only('edit', 'update');
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $user_list = $this->userService->getUsersList($request->all());
        return view('admin.user.index', compact('user_list'));
    }

    public function create()
    {
        $roles = app(RoleService::class)->getCacheAll();
        return view('admin.user.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $this->userService->store($request->all());
        return response()->json([
            'code' => 200,
            'msg' => '用户创建成功'
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all(); // 获取所有角色供选择
        return view('admin.user.edit', compact('user', 'roles'));
    }

    // 提交更新用户
    public function update(UserRequest $request , User $user)
    {
        $this->authorize('update', $user);
        $this->userService->update($user , $request->all());
        return response()->json(['code' => 200, 'msg' => '用户编辑成功！']);
    }

    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));
    }

    public function destroy(User $user  )
    {
        $this->authorize('delete', $user);
        $this->userService->destroy($user);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        $this->userService->batchDestroy($ids);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功'
        ]);
    }

    public function status(Request $request , User $user)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        $user = $this->userService->changeStatus($user, $request->status);
        return response()->json([
            'code'=>200,
            'status'=>$user->status,
            'msg' => '状态修改成功',
        ]);
    }
}
