<?php
namespace App\Services\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class MenuService extends BaseService
{
    public function __construct()
    {
        $this->modelClass = Menu::class;
        $this->cacheKey = 'menu_all';
    }

    protected $permissionSuffix = [
        'allSuffix' => [
            'index'   => '查看',
            'store'   => '新增',
            'update'  => '编辑',
            'destroy' => '删除',
            'audit'   => '审核',
            'export'  => '导出',
        ],
        'curdSuffix' => [
            'index'   => '查看',
            'store'   => '新增',
            'update'  => '编辑',
            'destroy' => '删除',
        ],
        'baseSuffix' => [
            'index' => '查看',
        ]
    ];

    public function getAuthMenu()
    {
        $user = Auth::user();
        if( empty($user) ) {
            return [];
        }
        //全局菜单
        $allMenu = $this->getCacheAll();

        //用户权限
        if( $user->id != 1 ){
            $rolePerms = $user->getAllPermissions()->pluck('name')->toArray();
            $menuFiltered = $this->filterMenu($allMenu, $rolePerms);
            return $menuFiltered;
        }else{
            return $allMenu;
        }
    }

    //对比全部菜单和角色权限菜单
    protected function filterMenu(array $menuTree, array $rolePerms): array
    {
        $filteredMenu = [];
        foreach ($menuTree as $menu) {
            if (!empty($menu['permission']) && in_array($menu['permission'], $rolePerms)) {
                $filteredMenu[] = $menu;
                continue;
            }
            if (!empty($menu['children']) && is_array($menu['children'])) {
                $filteredChildren = $this->filterMenu($menu['children'], $rolePerms);
                if (!empty($filteredChildren)) {
                    $menu['children'] = $filteredChildren; // 替换为过滤后的子菜单
                    $filteredMenu[] = $menu;
                }
            }
        }
        return $filteredMenu;
    }
    public function getCacheAll()
    {
        //$this->clearCache();
        return Cache::remember($this->getFullCacheKey(), $this->cacheTtl, function () {
            return Menu::where('status', 1)
                ->where('parent_id', 0)
                ->with('children','children.children')
                ->orderBy('sort')
                ->get()
                ->toArray();
        });
    }

    public function getMenusList($params)
    {
        if (isset($params['title'])) {
            $searchMenu = Menu::where('title', 'like', '%'.trim($params['title']).'%')->first();
            if ($searchMenu) {
                while ($searchMenu->parent_id != 0) {
                    $searchMenu = Menu::find($searchMenu->parent_id);
                }
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
        return $menu;
    }

    public function store(array $data): bool
    {
        try {
            DB::beginTransaction();
            $menu = Menu::create($data);
            // 自动生成权限
            if (!empty($data['permission']) && $data['create_permission_type'] != 0) {
                $suffix = $this->permissionSuffix[$data['create_permission_type']];
                $prefix = trim($data['permission_prefix']);
                $moduleName = trim($data['moduleName']);

                //判断是否存在路由，存在路由说明是最下级菜单，不能绑定在权限menu_id上，需要查询父级id
                if( isset($data['route']) && trim( $data['route']) != '' ) {
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
                            'module'     => $data['title'],
                            'guard_name' => 'web',
                        ]
                    );
                }
            }
            $this->clearCache();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($this->formatMsg('新增', $e->getMessage()));
        }
    }

    public function update( Model $model , array $data): bool
    {
        try {
            DB::beginTransaction();
            $model->update($data);
            // 如果填写了权限标识 → 自动生成权限
            if ($data['create_permission_type']!=0) {
                $suffix = $this->permissionSuffix[$data['create_permission_type']];
                $prefix = trim($data['permission_prefix']);
                $moduleName = trim($data['moduleName']);
                foreach ($suffix as $action => $title) {
                    $newPermName = $prefix . '.' . $action; // 新权限标识
                    $newPermTitle = $moduleName . '-' . $title; // 新权限中文名称
                    $permission = Permission::where('name',$newPermName)->first();

                    //判断是否存在路由，存在路由说明是最下级菜单，不能绑定在权限menu_id上，需要查询父级id
                    if( isset($data['route']) && trim( $data['route']) != '' ) {
                        $menu_id = $model->parent_id;
                    }else{
                        $menu_id = $model->id;
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
            $this->clearCache();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($this->formatMsg('修改', $e->getMessage()));
        }
    }

}
