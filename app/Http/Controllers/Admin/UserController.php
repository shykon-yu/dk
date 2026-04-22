<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\Admin\RoleService;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->middleware('can:admin.user.index')->only('index');
        $this->middleware('can:admin.user.store')->only('create', 'store');
        $this->middleware('can:admin.user.update')->only('edit', 'update','status');
        $this->middleware('can:admin.user.destroy')->only('destroy','batchDestroy');
        $this->userService = $userService;
    }
    /**
     * 用户列表页
     */
    public function index(Request $request)
    {
        $user_list = $this->userService->getUsersList($request->all());
        return view('admin.user.index', compact('user_list'));
    }

    /**
     * 新增页面
     */
    public function create()
    {
        $roles = app(RoleService::class)->getCacheAll();
        return view('admin.user.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        try {
            $this->userService->store($request->all());
            return response()->json([
                'code' => 200,
                'msg' => '用户创建成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg' => '创建失败：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 编辑页面
     */
    public function edit(User $user)
    {
        $roles = Role::all(); // 获取所有角色供选择
        return view('admin.user.edit', compact('user', 'roles'));
    }

    // 提交更新用户
    public function update(UserRequest $request , User $user)
    {
        try {
            $this->userService->update($user , $request->all());

            DB::commit();
            return response()->json(['code' => 200, 'msg' => '用户编辑成功！']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 500, 'msg' => '编辑失败：' . $e->getMessage()]);
        }
    }
    /**
     * 详情页面
     */
    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));
    }

    public function destroy(User $user  )
    {
        $this->authorize('delete', $user);
        try{
            $this->userService->destroy($user);
            return response()->json([
                'code' => 200,
                'msg' => '删除成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        if(empty($ids)){
            return response()->json([
                'code' => 400,
                'msg' => '请选择',
            ]);
        }
        try{
            $this->userService->batchDestroy($ids);
            return response()->json([
                'code' => 200,
                'msg' => '删除成功'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function status(Request $request , User $user)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $department = $this->userService->changeStatus($user, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$user->status,
                'msg' => '状态修改成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code'=>500,
                'msg'=>$e->getMessage(),
            ]);
        }
    }
}
