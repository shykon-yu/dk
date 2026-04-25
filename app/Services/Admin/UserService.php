<?php
namespace App\Services\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserService extends BaseService{
    public function __construct()
    {
        $this->modelClass = User::class;
        $this->cacheKey = 'goods_user_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return User::where('status' , 1)
                ->orderBy('sort' , 'asc')
                ->get();
        });
    }

    public function getUsersList($params)
    {
        $data = User::query()
            ->when(!empty($params['username']), function ($q) use ($params) {
                $q->where('username', 'like', "%{$params->username}%");
            })
            ->when(!empty($params['name']), function ($q) use ($params) {
                $q->where('name', 'like', "%{$params->name}%");
            })
            // 多对多查询：筛选属于某个部门的用户
            ->when(!empty($params['department_ids']), function ($q) use ($params) {
                $q->whereHas('departments', function ($subQuery) use ($params) {
                    $subQuery->whereIn('department_id', $params['department_ids']);
                });
            })
            // 无传参：普通管理员只能看自己部门下的用户
            ->when(empty($params['department_ids']) && Auth::user()->id != 1, function ($q) {
                $q->whereHas('departments', function ($subQuery) {
                    $subQuery->whereIn('department_id', Auth::user()->departments->pluck('id'));
                });
            })
            ->with(['roles', 'departments'])
            ->get();
        return $this->paginateCacheData($data, $params,50);
    }

    public function store(array $data): bool
    {
        $data['password'] = Hash::make($data['password']);
        try {
            DB::beginTransaction();
            // 创建用户
            $user = $this->getModelClass()::create($data);

            // 同步角色
            if (!empty($data['role_id'])) {
                $role = Role::find($data['role_id']);
                $user->syncRoles($role);
            }

            // 同步部门
            $deptIds = array_filter($data['department_id'] ?? [], fn($item) => is_numeric($item) && $item > 0);
            $user->departments()->sync($deptIds);

            // 更新缓存
            $this->clearCache();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($this->formatMsg('新增', $e->getMessage()));
        }
    }
    public function update(Model $model, array $data): bool
    {
        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        try {
            DB::beginTransaction();
            $model->update($data);

            // 同步角色
            if (!empty($data['role_id'])) {
                $role = Role::find($data['role_id']);
                $model->syncRoles($role);
            }

            // 同步部门
            $deptIds = array_filter($data['department_id'] ?? [], fn($item) => is_numeric($item) && $item > 0);
            $model->departments()->sync($deptIds);

            $this->clearCache();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($this->formatMsg('修改', $e->getMessage()));
        }
    }
    public function batchDestroy(array $ids): bool
    {
        try {
            $userId = auth()->id();
            $deleteIds = collect($ids)->filter(function ($id) use ($userId) {
                if ($id == $userId) {
                    return false;
                }
                if ($id == 1) {
                    return false;
                }
                return true;
            })->values();
            if ($deleteIds->isNotEmpty()) {
                User::destroy($deleteIds);
            }
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('批量删除', $e->getMessage()));
        }
    }
}
