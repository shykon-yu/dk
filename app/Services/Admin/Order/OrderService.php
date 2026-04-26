<?php
namespace App\Services\Admin\Order;
use App\Models\Order;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OrderService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Order::class;
        $this->cacheKey = 'orders_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Order::query()
                ->select(['id', 'name'])
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
        });
    }

    public function getOrdersList($params)
    {
        $data = $this->modelClass::query()
            ->when(!empty($params['order_code']), function ($q) use ($params) {
                $q->where('order_code', 'like', '%' . trim($params['order_code']) . '%');
            })
            ->when(!empty($params['goods_name']), function ($q) use ($params) {
                $q->whereHas('items.goods', function ($qq) use ($params) {
                    $qq->where('name', 'like', '%' . trim($params['goods_name']) . '%');
                });
            })
            ->when(!empty($params['customer_sku']), function ($q) use ($params) {
                $q->whereHas('items.goods', function ($qq) use ($params) {
                    $qq->where('customer_sku', 'like', '%' . trim($params['customer_sku']) . '%');
                });
            })
            ->when(!empty($params['department_ids']), function ($q) use ($params) {
                $q->whereIn('department_id', $params['department_ids']);
            })
            ->when(!empty($params['customer_ids']), function ($q) use ($params) {
                $q->whereIn('customer_id', $params['customer_ids']);
            })
            ->when(!empty($params['supplier_ids']), function ($q) use ($params) {
                $q->whereIn('supplier_id', $params['supplier_ids']);
            })
            ->when(!empty($params['status']), function ($q) use ($params) {
                $q->whereIn('status', $params['status']);
            }, function ($q) {
                $q->whereIn('status', [0,1,2]);
            })
            ->when(!empty($params['is_star']), function ($q) use ($params) {
                $q->whereIn('is_star', $params['is_star']);
            })
            ->orderBy('is_star', 'desc')
            ->with([
                'items','departments','customers','suppliers','creator','updater',
                'items.goods','items.goodsSkus'
                ])
            ->get();
       // dd($data);
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function store(array $data):bool
    {
        $data = $this->cleakkeys($data);//清洗数据
        [$data,$componentArray,$colorArray] = $this->separateData($data);//分离数据
        try {
            DB::beginTransaction();
            // 保存商品
            $goods = $this->getModelClass()::create($data);

            // 同步成分
            if (!empty($componentArray)) {
                $syncData = $this->syncComponentsData($componentArray);
                $goods->components()->sync($syncData);
            }

            // 保存SKU（颜色）
            if (!empty($colorArray)) {
                //$insertColors = $this->dealColor($colorArray, $goods->id);
                foreach( $colorArray as $color ) {
                    $color['goods_id'] = $goods->id;
                    $sku = Sku::create($color);
                    //生成库存数据
                    $stockData = $this->getStockArrayBySku($sku);
                    $stockData['warehouse_id'] = $data['warehouse_id'];
                    GoodsSkuStock::create($stockData);
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

    public function update(Model $model , array $data):bool
    {
        $data = $this->cleakkeys($data);//清洗数据
        [$data,$componentArray,$colorArray] = $this->separateData($data);//分离数据
        try {
            DB::beginTransaction();
            //如果图片地址发生变化，记录旧地址以便删除
            if( $model->main_image != $data['main_image'] ){
                $mainImagePath = $model->main_image;
                $thumbImagePath = $model->thumb_image;
            }else{
                $mainImagePath = null;
                $thumbImagePath = null;
            }

            //修改商品数据
            $model->update($data);

            // 同步成分
            if (!empty($componentArray)) {
                $syncData = $this->syncComponentsData($componentArray);
                $model->components()->sync($syncData);
            }

            //处理颜色
            if( !empty($colorArray) ) {
                $updateColors = [];//编辑数据
                $deleteColors = [];//删除数据
                $oldColors = $model->skus->toArray();//旧数据
                /**
                 * 遍历新数组和旧数据，对比id,有id将两个数组的这个id都删除
                 * 最后新数据剩余的就是新增数据，旧数据剩余的就是需要删除的数据
                 */
                foreach( $oldColors as $oldCol => $oldVal ){
                    foreach( $colorArray as $newCol => $newVal ){
                        if( $oldVal['id'] == $newVal['id'] ){
                            unset($newVal['stock']);//不能在商品编辑页面修改库存
                            $updateColors[] = $newVal;
                            unset($oldColors[$oldCol]);
                            unset($colorArray[$newCol]);
                        }
                    }
                }
                //新增颜色数据
                foreach( $colorArray as $newCol => $newVal ){
                    unset($newVal['id']);
                    $newVal['goods_id'] = $model->id;
                    $sku = Sku::create($newVal);
                    //生成库存数据
                    $stockData = $this->getStockArrayBySku($sku);
                    $stockData['warehouse_id'] = $data['warehouse_id'];
                    GoodsSkuStock::create($stockData);
                }
                //dd($updateColors);
                //编辑颜色数据
                foreach( $updateColors as $updateCol => $updateVal ){
                    Sku::where('id',$updateVal['id'])->update($updateVal);
                }

                //删除颜色数据
                foreach( $oldColors as $oldCol => $oldVal ){
                    $deleteColors[] = $oldVal['id'];
                }
                Sku::destroy($deleteColors);
            }
            $this->clearCache();
            DB::commit();
            if( !is_null($mainImagePath) ){
                $this->unlinkImage($mainImagePath);
                $this->unlinkImage($thumbImagePath);
            }
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($this->formatMsg('修改', $e->getMessage()));
        }
    }

    public function destroy(Model $model): bool
    {
        try {
            DB::beginTransaction();
            $model->delete();
            $model->skus()->delete();
            //$model->stocks()->delete();
            $this->clearCache();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($this->formatMsg($model->name.'删除', $e->getMessage()));
        }
    }

    public function batchDestroy(array $ids): bool
    {
        try {
            foreach( $ids as $id ){
                $goods = $this->getModelClass()::find($id);
                if( $goods ){
                    $this->destroy($goods);
                }
            }
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('批量删除', $e->getMessage()));
        }
    }

    //分离数据
    public function separateData(array $data):array
    {
        // 取出成分/颜色并从主数据移除
        $componentArray = $data['components'] ?? [];
        $colorArray     = $data['colors'] ?? [];
        unset($data['components'], $data['colors']);
        return [$data, $componentArray, $colorArray];
    }
    //清洗数据
    public function cleakkeys($data){
        foreach ($this->cleanKeys as $key) {
            if(isset($data[$key])){
                unset($data[$key]);
            }
        }
        return $data;
    }

    //同步成分格式
    public function syncComponentsData(array $data):array
    {
        $syncData = collect($data)->mapWithKeys(function ($item) {
            return [
                $item['component_id'] => ['percent' => $item['percent']]
            ];
        })->all();
        return $syncData;
    }

    public function getStockArrayBySku(Model $model):array
    {
        return [
            'sku_id' => $model->id,
            'stock' => $model->stock,
            'goods_id' => $model->goods_id,
        ];
    }
}
