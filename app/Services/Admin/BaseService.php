<?php
namespace App\Services\Admin;
use App\Services\Admin\Goods\GoodsSeasonService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

abstract class BaseService{
    protected string $modelClass;
    protected string $cacheKey;
    protected int $cacheTtl = 180 * 24 * 60 * 60;
    protected string $cachePrefix = 'admin_';
    public function store( array $data ):bool
    {
        try{
            $this->getModelClass()::create( $data );
            $this->clearCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception($this->formatMsg('新增', $e->getMessage()));        }
    }

    public function update( Model $model , array $data ):bool
    {
        try{
            $model->update( $data );
            $this->clearCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception($this->formatMsg('修改', $e->getMessage()));
        }
    }

    public function destroy(Model $model):bool
    {
        try{
            $model->delete();
            $this->clearCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception($this->formatMsg('删除', $e->getMessage()));
        }
    }

    public function batchDestroy(array $ids): bool
    {
        try{
            $this->getModelClass()::destroy($ids);
            $this->clearCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception($this->formatMsg('批量删除', $e->getMessage()));
        }
    }

    /**
     * 获取除了软删除之外的所有数据
     */
    public function getAllWithoutTrashed()
    {
        return $this->modelClass::query()
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function getFullCacheKey():string
    {
        return $this->cachePrefix.$this->cacheKey;
    }

    public function clearCache():void
    {
        Cache::forget($this->getFullCacheKey());
    }

    abstract public function getCacheAll();

    public function getModelClass()
    {
        return $this->modelClass;
    }

    // 统一异常消息格式化
    protected function formatMsg(string $action, string $detail): string
    {
        $modelName = class_basename($this->modelClass);
        return "{$action}{$modelName}失败：{$detail}";
    }
    protected function getPerPage()
    {
        return (new $this->modelClass)->getPerPage();
    }
    // 通用内存分页（缓存数据后，全局复用的分页逻辑）
    protected function paginateCacheData($collection, array $params, int $perPage = 20): LengthAwarePaginator
    {
        $page = $params['page'] ?? 1;
        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page
        );
        $paginator->setPath(request()->url());
        return $paginator;
    }

    public function changeStatus(Model $model,int $status):object
    {
        try{
            $model->status = $status;
            $model->save();
            $this->clearCache();
            return $model;
        }catch (\Exception $e){
            throw new \Exception($this->formatMsg('状态修改', $e->getMessage()));
        }
    }
}
