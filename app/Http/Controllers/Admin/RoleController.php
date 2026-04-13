<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission; // 你的自定义权限模型
use App\Models\Role;       // 你的自定义角色模型
use App\Services\ViewTableService;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request , ViewTableService $viewTableService )
    {
        $headers = $viewTableService->getHeaders('role');
        $name = $request->get('name');
        $roles = Role::query()
            ->when($name, function ($q) use ($name) {
                $q->where('name', 'like', "%$name%");
            })
            ->paginate(10);
        return view('admin.role.index', compact('roles', 'headers'));
    }
    // 渲染添加角色页面
    public function create()
    {
        $menuTree = Menu::with('children')->where('parent_id', 0)->get()->toArray();
        $permissionMap = Permission::all()->groupBy('menu_id');

        return view('admin.role.create', compact('menuTree', 'permissionMap'));
    }

    public function edit(Role $role)
    {
        $menuTree = Menu::with('children')->where('parent_id', 0)->get()->toArray();
        $permissionMap = Permission::all()->groupBy('menu_id');
        return view('admin.role.edit', compact('role', 'menuTree', 'permissionMap'));
    }

    public function store(RoleRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);
            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }
            DB::commit();
            return response()->json([
                'code' => 200,
                'msg' => '角色创建成功！',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => '创建失败：' . $e->getMessage(),
            ]);
        }
    }
    public function update(RoleRequest $request)
    {
        $validated = $request->validated();
        //dd($validated);
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($request->id);

            // 更新角色
            $role->update([
                'name' => $validated['name'],
            ]);

            // 同步权限
            $permissions = Permission::whereIn('id', $validated['permissions'] ?? [])->get();
            $role->syncPermissions($permissions);

            DB::commit();

            return response()->json([
                'code' => 200,
                'msg' => '角色更新成功！'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => '更新失败：' . $e->getMessage()
            ]);
        }
    }
}
