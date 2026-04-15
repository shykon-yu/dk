<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Scopes\ActiveScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 用户列表页
     */
    public function index(Request $request)
    {
        $user_list = User::query()
            ->with(['roles'])
            //->withoutGlobalScope(ActiveScope::class)//取消全局作用域
            ->when($request->user_name, function ($q) use ($request) {
                $q->where('user_name', 'like', "%{$request->user_name}%");
            })
            ->when($request->name, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->name}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(15);
        return view('admin.user.index', compact('user_list'));
    }

    /**
     * 新增页面
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.user.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            // 创建用户
            $user = User::create([
                'username' => $validated['username'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'open_id' => $validated['open_id'],
                'password' => Hash::make($validated['password']),
            ]);

            // 分配角色
            $role = Role::find($validated['role_id']);
            $user->assignRole($role);
            DB::commit();
            return response()->json([
                'code' => 200,
                'msg' => '用户创建成功'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
    public function update(UserRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction(); // 事务保证数据安全
        try {
            $user = User::findOrFail($request->id);
            // 组装更新数据，排除密码（单独处理）
            $updateData = [
                'username' => $validated['username'],
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'open_id' => $validated['open_id'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            $user->update($updateData);
            // 分配角色
            $role = Role::find($validated['role_id']);
            $user->assignRole($role);
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

    public function destroy(User $user)
    {
        // 安全判断
        if ($user->id == 1 || $user->id == Auth::id()) {
            return response()->json(['code' => 400, 'msg' => '该用户无法删除']);
        }

        DB::beginTransaction();
        try {
            $user->syncRoles([]); // 清空角色
            $user->delete();      // 删除用户

            DB::commit();
            return response()->json(['code' => 200, 'msg' => '删除成功']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => '删除失败：' . $e->getMessage()
            ]);
        }
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['code' => 400, 'msg' => '请选择要删除的用户']);
        }

        DB::beginTransaction();
        try {
            $users = User::whereIn('id', $ids)
                ->where('id', '!=', 1)          // 禁止删超管
                ->where('id', '!=', Auth::id())  // 禁止删自己
                ->get();

            if ($users->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'code' => 400,
                    'msg' => '无有效用户可删除（含超级管理员/自己）'
                ]);
            }

            foreach ($users as $user) {
                $user->syncRoles([]);
                $user->delete();
            }

            DB::commit();
            return response()->json([
                'code' => 200,
                'msg' => '批量删除成功，已解绑所有角色'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => '批量删除失败：' . $e->getMessage()
            ]);
        }
    }
}
