<?php
namespace App\Services\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class RoleService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Role::class;
        $this->cacheKey = 'goods_role_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Role::all();
        });
    }

    public function getUsersList($params)
    {
        $data = User::query()
            ->with(['roles'])
            //->withoutGlobalScope(ActiveScope::class)//取消全局作用域
            ->when(!empty($params->username), function ($q) use ($params) {
                $q->where('username', 'like', "%{$params->username}%");
            })
            ->when(!empty($params->name), function ($q) use ($params) {
                $q->where('name', 'like', "%{$params->name}%");
            })
            ->orderBy('id', 'asc')
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }
}
