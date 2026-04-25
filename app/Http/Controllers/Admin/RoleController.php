<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Admin\RoleService;
use App\Services\ViewTableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class RoleController extends Controller
{
    protected $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->middleware('permission:admin.roles.index')->only('index','show');
        $this->middleware('permission:admin.roles.store')->only('create','store');
        $this->middleware('permission:admin.roles.update')->only('edit','update');
        $this->middleware('permission:admin.roles.destroy')->only('destroy','batchDestroy');
        $this->roleService = $roleService;
    }
    public function index(Request $request , ViewTableService $viewTableService )
    {

        $roles = $this->roleService->getRolesList($request->all());
        return view('admin.role.index', compact('roles'));
    }
    public function create()
    {
        $permissionMap = Permission::all()
            ->groupBy('menu_id')
            ->map(function ($permissions, $menuId) {
                return [
                    'menu_id'  => $menuId,
                    'module'   => $permissions->first()->module,
                    'children' => $permissions,
                ];
            })
            ->values();
        return view('admin.role.create', compact('permissionMap'));
    }

    public function edit(Role $role)
    {
        $permissionMap = Permission::all()
            ->groupBy('menu_id')
            ->map(function ($permissions, $menuId) {
                return [
                    'menu_id'  => $menuId,
                    'module'   => $permissions->first()->module,
                    'children' => $permissions,
                ];
            })
            ->values();
        return view('admin.role.edit', compact('role', 'permissionMap'));
    }

    public function store(RoleRequest $request)
    {
        try {
            $this->roleService->store($request->validated());
            return response()->json([
                'code' => 200,
                'msg' => '角色创建成功！',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg' => '创建失败：' . $e->getMessage(),
            ]);
        }
    }
    public function update(RoleRequest $request , Role $role)
    {
        try {
            $this->roleService->update($role,$request->validated());
            return response()->json([
                'code' => 200,
                'msg' => '角色更新成功！'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg' => '更新失败：' . $e->getMessage()
            ]);
        }
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);
        try{
            $this->roleService->destroy($role);
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
            $this->roleService->batchDestroy($ids);
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
}
