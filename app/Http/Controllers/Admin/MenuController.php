<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\MenuRequest;
use App\Models\Permission;
use App\Services\ViewTableService;

class MenuController extends Controller
{
    // 完整权限
    protected $permissionSuffix = [
        'allSuffix' => [
            'view'   => '查看',
            'create'   => '新增',
            'update'  => '编辑',
            'delete' => '删除',
            'audit'   => '审核',
            'export'  => '导出',
        ],
        'curdSuffix' => [
            'view'   => '查看',
            'create'   => '新增',
            'update'  => '编辑',
            'delete' => '删除',
        ],
        'baseSuffix' => [
            'view' => '查看',
        ]
    ];

    // 提取权限前缀 + 模块名
    protected function getPermissionPrefixAndModuleName($permission, $title)
    {
        $prefix = implode('.', array_slice(explode('.', $permission), 0, -1));
        $moduleName = str_replace('管理', '', $title);

        return [$prefix, $moduleName];
    }

    // 菜单列表（三级）
    public function index(Request $request, ViewTableService $tableHeaderService)
    {
        $headers = $tableHeaderService->getHeaders('menu');
        // 搜索关键词去空格
        $title = trim($request->title);
        if ($title) {
            $searchMenu = Menu::where('title', 'like', "%{$title}%")->first();
            if ($searchMenu) {
                while ($searchMenu->parent_id != 0) {
                    $searchMenu = Menu::find($searchMenu->parent_id);
                }
                //dd($searchMenu);
                $menu = Menu::where('id', $searchMenu->id)
                    ->with(['children', 'children.children'])
                    ->orderBy('sort')
                    ->get()
                    ->toArray();
            } else {
                $menu = [];
            }
        } else {
            // 无搜索，正常查顶级
            $menu = Menu::where('parent_id', 0)
                ->with(['children', 'children.children'])
                ->orderBy('sort')
                ->get()
                ->toArray();
        }

        return view('admin.menu.index', compact('menu', 'headers'));
    }

    //新增页面
    public function create()
    {
        $allMenus = Menu::where('parent_id', 0)
            ->with('children')
            ->orderBy('sort')
            ->get()
            ->toArray();
        return view('admin.menu.create', compact('allMenus'));
    }
    // 编辑页面
    public function edit(Menu $menu)
    {
        $allMenus = Menu::where('id', '!=', $menu->id)
            ->where('parent_id', 0)
            ->with('children')
            ->orderBy('sort')
            ->get()
            ->toArray();
        return view('admin.menu.edit', compact('menu', 'allMenus'));
    }

    // 更新菜单
    public function update(MenuRequest $request)
    {
        $validated = $request->validated();

        try {
            $menu = Menu::findOrFail($validated['id']);
            $menu->update($validated);

            // 如果填写了权限标识 → 自动生成权限
            if ($request->post('create_permission_type')!=0) {
                $suffix = $this->permissionSuffix[$request->post('create_permission_type')];
                $prefix = trim($request->post('permission_prefix'));
                $moduleName = trim($request->post('moduleName'));
                foreach ($suffix as $action => $title) {
                    $newPermName = $prefix . '.' . $action; // 新权限标识
                    $newPermTitle = $moduleName . '-' . $title; // 新权限中文名称
                    //$permission = Permission::where('name', 'like', '%.' . $action)
                    $permission = Permission::where('name',$newPermName)->first();

                    //判断是否存在路由，存在路由说明是最下级菜单，不能绑定在权限menu_id上，需要查询父级id
                    if( $request->has('route') && trim($request->post('route')) != '' ) {
                        $menu_id = $menu->parent_id;
                    }else{
                        $menu_id = $menu->id;
                    }

                    if ($permission) {
                            $permission->update([
                                'title' => $newPermTitle,
                                'module' => $moduleName,
                                'menu_id' => $menu_id,
                            ]);
                    } else {
                        Permission::create([
                            'menu_id'    => $menu_id,
                            'name'       => $newPermName,
                            'title'      => $newPermTitle,
                            'module' => $moduleName,
                            'guard_name' => 'web',
                        ]);
                    }
                }
            }

            return response()->json([
                'code' => 200,
                'msg'  => '菜单修改成功，权限已同步！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg'  => '修改失败：' . $e->getMessage()
            ]);
        }
    }

    // 新增菜单
    public function store(MenuRequest $request)
    {
        $validated = $request->validated();
        try {
            $menu = Menu::create($validated);

            // 自动生成权限
            if (!empty($validated['permission']) && $request->post('create_permission_type')!=0) {
                $suffix = $this->permissionSuffix[$request->post('create_permission_type')];
                $prefix = trim($request->post('permission_prefix'));
                $moduleName = trim($request->post('moduleName'));
//                [$prefix, $moduleName] = $this->getPermissionPrefixAndModuleName(
//                    $validated['permission'],
//                    $validated['title']
//                );
                //判断是否存在路由，存在路由说明是最下级菜单，不能绑定在权限menu_id上，需要查询父级id
                if( $request->has('route') && trim($request->post('route')) != '' ) {
                    $menu_id = $menu->id;
                }else{
                    $menu_id = $menu->parent_id;
                }

                foreach ($suffix as $action => $title) {
                    Permission::updateOrCreate(
                        [
                            'menu_id' => $menu_id,
                            'name' => $prefix . '.' . $action,
                            'title'      => $moduleName . '-' . $title,
                            'module'     => $validated['title'],
                            'guard_name' => 'web',
                        ]
                    );
                }
            }

            return response()->json([
                'code' => 200,
                'msg'  => '菜单新增成功，权限已生成！'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg'  => '新增失败：' . $e->getMessage()
            ]);
        }
    }

    // 删除菜单
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->json([
            'code' => 200,
            'msg'  => '删除成功'
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:menus,id'
        ]);

        Menu::destroy($request->ids);

        return response()->json([
            'code' => 200,
            'msg'  => '批量删除成功'
        ]);
    }
}
