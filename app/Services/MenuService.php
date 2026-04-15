<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class MenuService
{
    protected $cacheMenuKey = 'sys:menu:all';//所有菜单缓存
    protected $cacheRolePermKey = 'sys:role:perm';//用户有权的菜单
    protected $menuTtl = 24 * 60 * 60;
    protected $roleTtl = 1 * 60 * 60;
    public function getAuthMenu()
    {
        $user = Auth::user();
        if( empty($user) ) {
            return [];
        }
        //全局菜单
        $allMenu = $this->getAllMenu();
        //dd($allMenu);
        //用户权限
        //$rolePerms = $this->getRolePermissions($user->role_id);
//        //过滤
//        $menuFiltered = $allMenu->filer(function ($item) use ($userPerms) {
//            return in_array('*', $userPerms)
//                ||empty($item->permission)
//                ||in_array($item->permission, $userPerms);
//        });
        $menuFiltered = $allMenu;
        return $this->buildTree($menuFiltered);
    }
    protected function getAllMenu()
    {
        return Cache::remember($this->cacheMenuKey, $this->menuTtl, function () {
            return DB::table('menus')
                ->where('status', 1)
                ->whereNull('deleted_at') // 加上这一行即可排除已删除
                ->orderBy('sort')
                ->get()
                ->map(function ($item) {
                    return (array)$item;
                });
        });
    }

    protected function getRolePermissions($role_id)
    {
        if( !$role_id ) {
            return [];
        }
        if( Auth::user()->isSuperAdmin() ){
            return ['*'];
        }
        return Cache::remember($this->cacheRolePermKey . $role_id, $this->roleTtl, function()use($role_id) {
           return DB::table('role_has_permissions')
               ->where('role_id', $role_id)
               ->pluck('permission_name')
               ->toArray();
        });
    }

    protected function buildTree($menuFiltered, $parentId = 0)
    {
        $menus = [];
        foreach ($menuFiltered as $menu) {
            if( $menu['parent_id'] == $parentId ) {
                $children = $this->buildTree($menuFiltered, $menu['id']);
                if( $children ) {
                    $menu['children'] = $children;
                }
                $menus[] = $menu;
            }
        }
        return $menus;
    }
    /**
     * 清理菜单缓存（菜单修改时调用）
     */
    public function clearMenuCache()
    {
        Cache::forget($this->cacheMenuKey);
    }

    /**
     * 清理用户权限缓存（角色修改时调用）
     */
    public function clearUserPermCache($role_id)
    {
        Cache::forget($this->cacheRolePermKey . $role_id);
    }
}
