<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\Admin\MenuService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\MenuRequest;
use App\Models\Permission;

class MenuController extends Controller
{
    protected $menuService;
    public function __construct( MenuService $menuService)
    {
        $this->middleware('permission:admin.menus.index')->only('index');
        $this->middleware('permission:admin.menus.store')->only('create', 'store');
        $this->middleware('permission:admin.menus.update')->only('edit', 'update','status');
        $this->middleware('permission:admin.menus.destroy')->only('destroy','batchDestroy');
        $this->menuService = $menuService;
    }

    // 菜单列表（三级）
    public function index(Request $request )
    {
        $menu = $this->menuService->getMenusList($request->all());
        return view('admin.menu.index', compact('menu'));
    }

    //新增页面
    public function create()
    {
        $allMenus = $this->menuService->getCacheAll();
        return view('admin.menu.create', compact('allMenus'));
    }

    public function store(MenuRequest $request)
    {
        try {
            $this->menuService->store($request->validated());
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

    public function edit(Menu $menu)
    {
        $allMenus = $this->menuService->getCacheAll();
        return view('admin.menu.edit', compact('menu', 'allMenus'));
    }

    public function update(Menu $menu , MenuRequest $request)
    {
        try {
            $this->menuService->update($menu , $request->validated());
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


    public function destroy(Menu $menu)
    {
        try{
            $this->menuService->destroy($menu);
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
            $this->menuService->batchDestroy($ids);
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
