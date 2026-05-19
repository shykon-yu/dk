<?php
namespace App\Services\Admin\Order;
use App\Enums\OrderStatusEnum;
use App\Models\GoodsSkuStock;
use App\Models\Inbound;
use App\Models\InboundItem;
use App\Models\OrderItem;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class InboundService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Inbound::class;
        $this->cacheKey = 'inbounds_all';
    }

    public function getCacheAll(){}

    public function getInboundsList($params)
    {
        $data = $this->modelClass::query()
            ->has('items')
            ->when(!empty($params['inbound_code']), function ($q) use ($params) {
                $q->where('inbound_code', 'like', '%' . trim($params['inbound_code']) . '%');
            })
            ->when(!empty($params['order_code']), function ($q) use ($params) {
                $q->whereHas('items.orderItem.order', function ($qq) use ($params) {
                    $qq->where('order_code', 'like', '%' . trim($params['order_code']) . '%');
                });
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
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->where('inbound_at', '>=', $params['start_date']);
            },
            function ($q) {
                $q->where('inbound_at', '>=', today()->subMonths(8));
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->where('inbound_at', '<=', $params['end_date']);
            })
            ->orderBy('inbound_at', 'desc')
            ->with([
                'items','items.orderItem','items.orderItem.order','department','customer','supplier','creator','updater',
                'items.goods','items.sku'
                ])
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function getItems($params)
    {
        $data = InboundItem::query()
            ->when(!empty($params['inbound_code']), function ($q) use ($params) {
                $q->whereHas('inbound', function ($qq) use ($params) {
                    $qq->where('inbound_code', 'like', '%' . trim($params['inbound_code']) . '%');
                });
            })
            ->when(!empty($params['order_code']), function ($q) use ($params) {
                $q->whereHas('orderItem.order', function ($qq) use ($params) {
                    $qq->where('order_code', 'like', '%' . trim($params['order_code']) . '%');
                });
            })
            ->when(!empty($params['goods_name']), function ($q) use ($params) {
                $q->whereHas('goods', function ($qq) use ($params) {
                    $qq->where('name', 'like', '%' . trim($params['goods_name']) . '%');
                });
            })
            ->when(!empty($params['customer_sku']), function ($q) use ($params) {
                $q->whereHas('goods', function ($qq) use ($params) {
                    $qq->where('customer_sku', 'like', '%' . trim($params['customer_sku']) . '%');
                });
            })
            ->when(!empty($params['department_ids']), function ($q) use ($params) {
                $q->whereIn('o.department_id', $params['department_ids']);
            })
            ->when(!empty($params['customer_ids']), function ($q) use ($params) {
                $q->whereIn('o.customer_id', $params['customer_ids']);
            })
            ->when(!empty($params['supplier_ids']), function ($q) use ($params) {
                $q->whereIn('o.supplier_id', $params['supplier_ids']);
            })
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->where('o.inbound_at', '>=', $params['start_date']);
            },
            function ($q) {
                $q->where('o.inbound_at', '>=', today()->subMonths(8));
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->where('o.inbound_at', '<=', $params['end_date']);
            })
            ->leftJoin('inbounds as o','o.id','=','inbound_items.inbound_id')
            ->orderBy('o.inbound_at', 'desc')
            ->with([
                'inbound','orderItem','orderItem.order','inbound.department','inbound.customer','inbound.supplier','inbound.creator','inbound.updater',
                'goods','sku'
            ])
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function store(array $data):bool
    {
        try {
            //防止重复提交
            $lockKey = "inbound:submit:" . $data['inbound_code'];
            if (!Cache::add($lockKey, 'locked', 60)) {
                throw new \Exception('请勿重复提交', 429);
            }

            DB::beginTransaction();
            $items = $data['goods'];
            unset($data['goods']);

            //创建总单
            $inbound = $this->getModelClass()::create($data);

            if (empty($items)) {
                throw new \Exception('至少添加一个产品', 400);
            }

            //创建子单
            $this->createInboundItems($items,$inbound);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Cache::forget($lockKey);
            //dd($e->getMessage(), $e->getFile(), $e->getLine());
            throw new \Exception('新增失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(Model $model , array $data):bool
    {
        try {
            $items = $data['goods'];
            unset($data['goods']);
            unset($data['department_id'],$data['customer_id'],$data['inbound_code']);

            $oldWarehouseId = $model->warehouse_id;
            $newWarehouseId = $data['warehouse_id'];
            DB::beginTransaction();
            $model->update($data);

            if (empty($items)) {
                throw new \Exception('至少添加一个产品', 400);
            }

            //处理子单
            $updates = [];//编辑数据
            $newItems = [];
            // 旧数据：ID => 旧数据（构建哈希映射）
            $oldItems = collect($model->items)->keyBy('id')->toArray();
            foreach ($items as $item) {
                $id = $item['id'] ?? 0;

                // 存在：编辑
                if ($id && isset($oldItems[$id])) {
                    $old = $oldItems[$id];
                    $item['old_quantity'] = $old['quantity'];
                    $item['quantity_diff'] = $item['quantity'] - $old['quantity'];

                    $updates[] = $item;
                    unset($oldItems[$id]); // 删掉已匹配的
                }
                // 不存在：新增
                else {
                    $newItems[] = $item;
                }
            }

            //新增子单数据
            if(!empty($newItems)){
                $this->createInboundItems($newItems,$model);
            }

            //编辑数据
            if( !empty($updates) ){
                $this->updateInboundItems($updates,$model,$oldWarehouseId,$newWarehouseId);
            }

            //删除数据
            if( !empty($oldItems) ){
                $this->deleteInboundItems($oldItems,$model);
            }


            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('修改失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function createInboundItems(array $items,Model $inbound)
    {
        // 保存子单
        foreach( $items as $item ) {
            unset($item['id']);
            $item['inbound_id'] = $inbound->id;
            $item['amount'] = bcmul($item['quantity'],$item['price'],2);
            InboundItem::create($item);

            // 更新订单收货数量 & 状态
            $this->updateOrderItemReceivedQuantity($item,$item['quantity']);

            // 更新库存
            $this->updateGoodsSkuStock($item, $inbound->warehouse_id , $item['quantity'] , $inbound);
        }
    }

    public function updateInboundItems(array $items,Model $inbound,$oldWarehouseId,$newWarehouseId)
    {
        foreach( $items as $item ){
            $oldQuantity = $item['old_quantity'];//旧数量
            $quantityDiff = $item['quantity_diff'];
            unset($item['old_quantity'],$item['quantity_diff']);
            $item['amount'] = bcmul($item['quantity'],$item['price'],2);
            InboundItem::where('id',$item['id'])->update($item);
            $this->updateOrderItemReceivedQuantity($item,$quantityDiff);

            if( $oldWarehouseId == $newWarehouseId ){
                //如果没更换仓库，库存就新增差值部分
                $this->updateGoodsSkuStock($item,$inbound->warehouse_id,$quantityDiff);
            }else{
                //如果更换了仓库，原仓库扣除老数量，新仓库增加前端传过来的数量
                $this->updateGoodsSkuStock($item,$oldWarehouseId,-$oldQuantity);
                $this->updateGoodsSkuStock($item,$newWarehouseId,$item['quantity']);
            }

        }
    }

    public function deleteInboundItems(array $items ,Model $inbound)
    {
        $deletes = [];
        foreach( $items as $item ){
            //删除订单的入库数量
            $this->updateOrderItemReceivedQuantity($item,-$item['quantity']);
            //删除库存中的数量
            $this->updateGoodsSkuStock($item,$inbound->warehouse_id,-$item['quantity']);
            $deletes[] = $item['id'];
        }
        if(!empty($deletes))  InboundItem::destroy($deletes);
    }


    /**
     * 更新订单明细的【已入库数量】和【入库状态】
     * 支持：新增入库 + 编辑入库（增减数量）
     *
     * @param array $item 入库单子单数据（必须包含 order_item_id）
     * @param int $increaseQuantity 要增加/减少的数量（正数=增加，负数=减少）
     * @return void
     * @throws \Exception
     */
    public function updateOrderItemReceivedQuantity(array $item,int $increaseQuantity)
    {
        // 如果没有关联订单明细，直接跳过
        if (empty($item['order_item_id']) || $item['order_item_id'] <= 0) {
            return;
        }

        // 查询订单明细
        $orderItem = OrderItem::find($item['order_item_id']);
        if (!$orderItem) {
            throw new \Exception('订单明细不存在');
        }

        // 累加/扣减 入库数量
        $orderItem->received_quantity += $increaseQuantity;

        // 判断订单状态：已全部入库 | 部分入库
        $orderItem->status = $orderItem->received_quantity >= $orderItem->number
            ? OrderStatusEnum::ALL_STOCK  // 已全部入库
            : OrderStatusEnum::PART_STOCK; // 部分入库

        // 保存到数据库
        $orderItem->save();
    }

    public function destroy(Model $model): bool
    {
        try {
            DB::beginTransaction();
            $items = $model->items->toArray();
            $this->deleteInboundItems($items, $model);
            $model->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('删除失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
