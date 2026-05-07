<?php
namespace App\Services\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    public function getRolesList($params)
    {
        $data = Role::query()
            ->when(!empty($params['name']), function ($q) use ($params) {
                $q->where('name', 'like', '%' . trim($params['name']) . '%');
            })
            ->get();
        return $this->paginateCacheData($data, $params,50);
    }

    public function store(array $data): bool
    {
        try {
            DB::beginTransaction();
            $role = Role::create([
                'name' => $data['name'],
                'level' => $data['level'],
                'guard_name' => 'web',
            ]);
            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'])->get();
                $role->syncPermissions($permissions);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('新增失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(Model $model, array $data): bool
    {
        try {
            DB::beginTransaction();
            $model->update([
                'name' => $data['name'],
                'level' => $data['level'],
            ]);
            // 同步权限
            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'] ?? [])->get();
                $model->syncPermissions($permissions);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('修改失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function batchDestroy(array $ids): bool
    {
        try {
            if(empty($ids)){
                throw new \Exception('请选择',400);
            }
            $userHasRole = Auth::user()->roles()->pluck('id')->toArray();

            $deleteIds = collect($ids)->filter(function ($id) use ($userHasRole) {
                if( in_array($id , $userHasRole) ){
                    throw new \Exception('不能删除自己角色');
                }
                $role = Role::query()->find($id);
                $currentLevel = Auth::user()->roles->sortBy('level')->first()?->level ?? 999;//当前最高层级
                $targetLevel = $role->level;//目标层级
                if( $currentLevel > $targetLevel ){
                    throw new \Exception('不能删除上级');
                }
                if( $role->name == "管理员" ){
                    throw new \Exception('不能删除管理员');
                }
                return true;
            });
            $this->getModelClass()::destroy($deleteIds);
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception('批量删除失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
