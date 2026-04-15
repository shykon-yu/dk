<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
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
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($request->id);

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

    public function destroy(Role $role)
    {
        if ($role->name === '管理员') {
            return response()->json(['code' => 400, 'msg' => '管理员无法删除']);
        }

        DB::beginTransaction();
        try {
            $role->permissions()->detach();
            $role->delete();
            DB::commit();

            return response()->json(['code' => 200, 'msg' => '删除成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 500, 'msg' => '删除失败：' . $e->getMessage()]);
        }
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['code' => 400, 'msg' => '请选择要删除的角色']);
        }
        DB::beginTransaction();
        try {
            $roles = Role::whereIn('id', $ids)
                ->where('name', '!=', '管理员')
                ->get();

            foreach ($roles as $role) {
                $role->permissions()->detach();
                $role->delete();
            }

            DB::commit();
            return response()->json(['code' => 200, 'msg' => '批量删除成功']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => '批量删除失败：' . $e->getMessage()
            ]);
        }
    }
}
