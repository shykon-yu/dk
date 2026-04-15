<?php
namespace App\Services\Admin\Goods;

use App\Http\Requests\Admin\GoodsSeasonRequest;
use App\Models\GoodsSeason;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class GoodsSeasonService{
    protected $goodsSeason;
    public function __construct(GoodsSeason $goodsSeason)
    {
        $this->goodsSeason = $goodsSeason;
    }

    public function storeGoodsSeason( $data )
    {
        try{
            $this->goodsSeason->create( $data );
            $this->flushGoodsSeasonsCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception( '新增失败'.$e->getMessage() );
        }
    }

    public function updateGoodsSeason( GoodsSeason $goodsSeason,$data )
    {
        try{
            $goodsSeason->update( $data );
            $this->flushGoodsSeasonsCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception( $e->getMessage() );
        }
    }

    public function destroyGoodsSeason(GoodsSeason $goodsSeason)
    {
        try{
            $goodsSeason->delete();
            $this->flushGoodsSeasonsCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception( $e->getMessage() );
        }
    }

    public function batchDestroyGoodsSeason($ids)
    {
        try{
            $this->goodsSeason->destroy($ids);
            $this->flushGoodsSeasonsCache();
            return true;
        }catch ( \Exception $e ){
            throw new \Exception( $e->getMessage() );
        }
    }

    /**
     * 所有商品季节
     */
    public function getGoodsSeasonsAll()
    {
        return Cache::remember('goodsSeasonsList', 60*60*24*365 , function(){
            return $this->goodsSeason
                //->orderBy('status', 'desc')
                ->orderBy('year', 'desc')
                ->orderBy('season', 'desc')
                ->get();
        });
    }

    public function getGoodsSeasonsList($params)
    {
        $data = $this->getGoodsSeasonsAll(); // 从缓存拿全部

        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        if (isset($params['year'])) {
            $data = $data->where('year', $params['year']);
        }

        if (isset($params['season'])) {
            $data = $data->where('season', $params['season']);
        }

        // 分页（内存分页）
        $perPage = $this->goodsSeason->getPerPage();
        $page = $params['page'] ?? 1;
        return new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            $data->count(),
            $perPage,
            $page
        );
    }
    /**
     * 年份列表
     */
    public function getYearsOptions()
    {
        return $this->getGoodsSeasonsAll()
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values();     // 重置索引（可选，让数组索引从0开始）
    }

    public function changeStatus(GoodsSeason $model,$status)
    {
        try{
            $model->status = $status;
            $model->save();
            $this->flushGoodsSeasonsCache();
            return $model;
        }catch (\Exception $e){
            throw new \Exception('状态修改失败'.$e->getMessage());
        }
    }

    public function flushGoodsSeasonsCache()
    {
        Cache::forget('goodsSeasonsList');
    }
}
