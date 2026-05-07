<?php
namespace App\Services\Admin\Order;
use App\Models\Order;
use App\Models\OrderExcel;
use App\Models\OrderItem;
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
            ->orderBy('created_at', 'desc')
            ->with([
                'items','departments','customers','suppliers','creator','updater',
                'items.goods','items.goodsSkus'
                ])
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function store(array $data):bool
    {
        try {
            //防止重复提交
            $lockKey = "order:submit:" . $data['order_code'];
            if (!Cache::add($lockKey, 'locked', 60)) {
                throw new \Exception('请勿重复提交', 429);
            }

            DB::beginTransaction();
            $items = $data['goods'];
            unset($data['goods']);
            $order = $this->getModelClass()::create($data);

            // 保存子单
            if (!empty($items)) {
                foreach( $items as $item ) {
                    unset($item['money']);
                    $item['order_id'] = $order->id;
                    OrderItem::create($item);
                }
            }
            //如果excel_id不是0，修改excel表的order_id数据，后期定时计划清理没有order_id的数据
            if( $data['excel_id'] != 0 ){
                $orderExcel = OrderExcel::query()->where('id',$data['excel_id'])->first();
                if(!empty($orderExcel)) $orderExcel->update(['order_id' => $order->id]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('新增失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(Model $model , array $data):bool
    {
        $items = $data['goods'];
        unset($data['goods']);
        unset($data['department_id'],$data['customer_id'],$data['order_code']);
        try {
            DB::beginTransaction();
            if( $model->excel_id != 0 && $model->excel_id != $data['excel_id'] ){
                $model->excel()->delete();
            }
            $model->update($data);

            //处理子单
            if( !empty($items) ) {
                $updates = [];//编辑数据
                $deletes = [];//删除数据
                $oldDatas = $model->items->toArray();//旧数据
                /**
                 * 遍历新数组和旧数据，对比id,有id将两个数组的这个id都删除
                 * 最后新数据剩余的就是新增数据，旧数据剩余的就是需要删除的数据
                 */
                foreach( $oldDatas as $oldKey => $old ){
                    foreach( $items as $itemKey => $item ){
                        if( $old['id'] == $item['id'] ){
                            $updates[] = $item;
                            unset($oldDatas[$oldKey]);
                            unset($items[$itemKey]);
                        }
                    }
                }

                //新增子单数据
                if(!empty($items)){
                    foreach( $items as $item ) {
                        unset($item['id']);
                        unset($item['money']);
                        $item['order_id'] = $model->id;
                        OrderItem::create($item);
                    }
                }

                //编辑数据
                if( !empty($updates) ){
                    foreach( $updates as $update ){
                        unset($update['money']);
                        OrderItem::where('id',$update['id'])->update($update);
                    }
                }

                //删除数据
                foreach( $oldDatas as $old ){
                    $deletes[] = $old['id'];
                }
                if(!empty($deletes))  OrderItem::destroy($deletes);
            }

            if( $data['excel_id'] != 0 ){
                $orderExcel = OrderExcel::query()->where('id',$data['excel_id'])->first();
                if(!empty($orderExcel)) $orderExcel->update(['order_id' => $model->id]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('修改失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function destroy(Model $model): bool
    {
        try {
            DB::beginTransaction();
            $model->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('删除失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
