<?php
namespace App\Services\Admin\Order;
use App\Enums\OrderStatusEnum;
use App\Models\GoodsSkuStock;
use App\Models\Outbound;
use App\Models\OutboundItem;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OutboundService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Outbound::class;
        $this->cacheKey = 'outbounds_all';
    }

    public function getCacheAll(){}

    public function getOutboundsList($params,$isLogistics = false)
    {
        $query = $this->modelClass::query()
            ->has('items')
            ->when(!empty($params['outbound_code']), function ($q) use ($params) {
                $q->where('outbounds.outbound_code', 'like', '%' . trim($params['outbound_code']) . '%');
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
                $q->whereIn('outbounds.department_id', $params['department_ids']);
            })
            ->when(!empty($params['customer_ids']), function ($q) use ($params) {
                $q->whereIn('outbounds.customer_id', $params['customer_ids']);
            })
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->where('outbounds.outbound_at', '>=', $params['start_date']);
            },
            function ($q) {
                $q->where('outbounds.outbound_at', '>=', today()->subMonths(8));
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->where('outbounds.outbound_at', '<=', $params['end_date']);
            })
            ->orderBy('outbounds.outbound_at', 'desc');

            if ($isLogistics) {
                $query->selectRaw('outbounds.outbound_at,outbounds.customer_id,outbounds.clearance_id,outbounds.payment_id,
                ANY_VALUE(outbounds.department_id) as department_id,
                IFNULL(SUM(outbound_items.quantity), 0) as logistics_quantity,
                IFNULL(SUM(outbound_items.amount), 0) as logistics_amount
                ')
                    ->leftJoin('outbound_items', 'outbounds.id','=', 'outbound_items.outbound_id')
                    ->groupBy('outbounds.outbound_at', 'outbounds.customer_id', 'outbounds.clearance_id', 'outbounds.payment_id')
                ->with(['department','customer','clearance','payment']);
            }else{
                $query->with([
                    'items', 'department', 'customer', 'creator', 'updater',
                    'items.goods', 'items.sku'
                ]);
            }
            return $query->paginate($this->getPerPage());
    }

    public function getItems($params)
    {
        $data = OutboundItem::query()
            ->when(!empty($params['outbound_code']), function ($q) use ($params) {
                $q->whereHas('outbound', function ($qq) use ($params) {
                    $qq->where('outbound_code', 'like', '%' . trim($params['outbound_code']) . '%');
                });            })
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
                $q->whereHas('inbound', function ($qq) use ($params) {
                    $qq->whereIn('department_id', $params['department_ids']);
                });
            })
            ->when(!empty($params['customer_ids']), function ($q) use ($params) {
                $q->whereHas('inbound', function ($qq) use ($params) {
                    $qq->whereIn('customer_id', $params['customer_ids']);
                });
            })
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->where('o.outbound_at', '>=', $params['start_date']);
            },
            function ($q) {
                $q->where('o.outbound_at', '>=', today()->subMonths(8));
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->where('o.outbound_at', '<=', $params['end_date']);
            })
            ->orderBy('o.outbound_at', 'desc')
            ->leftJoin('outbounds as o', 'o.id','=', 'outbound_items.outbound_id')
            ->with([
                'outbound','outbound.department','outbound.customer','outbound.creator','outbound.updater',
                'goods','sku'
            ])
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    //获取物流报表页面子单
    public function getLogisticsItems($params)
    {
        $outbounds = $this->getModelClass()::query()
            ->where('customer_id', $params['customer_id'])
            ->where('clearance_id', $params['clearance_id'])
            ->where('payment_id', $params['payment_id'])
            ->where('outbound_at',$params['outbound_at'])
            ->with('items','items.goods','items.sku','items.craftMethod','department')
            ->get();
        $allItems = collect();

        //将子集合并
        foreach ($outbounds as $outbound) {
            $allItems = $allItems->merge($outbound->items);
        }

        //按照唛头 产品 价格 进行排序，方便后续做合并处理
        $sortedItems = $allItems->sortBy([
            ['shipping_mark','asc'],
            ['carton_no_start', 'asc'],
            ['goods_id', 'asc'],
            ['price', 'asc'],
        ])->values();

        $groupItems = $sortedItems->groupBy(fn($item) =>
            $item->shipping_mark.'-'.$item->carton_no_start.'-'.$item->carton_no_end.'-'.$item->goods_id.'-'.$item->price
        )->values()->map(function ($items) {
            $first = $items->first();
            $shippingMark = $first->carton_no_start == $first->carton_no_end
                ? $first->shipping_mark.'-'.$first->carton_no_start
                : $first->shipping_mark.'-'.$first->carton_no_start.'~'.$first->carton_no_end;
            $first->shipping_mark_text = $shippingMark;
            $first->color_text = $items->map(fn($i) => $i->sku->color.' '.$i->quantity)->implode(' ');
            $first->quantity = $items->sum('quantity');
            $first->unit_carton_qty = $items->sum('unit_carton_qty');
            $first->amount = $items->sum('amount');

            return $first;
        });
        return $groupItems;
    }

    //通过传参过来的字段获取合并坐标
    public function getFieldMerge($groupItems, $fields)
    {
        $result = array_fill_keys($fields, []);

        $last   = array_fill_keys($fields, null);
        $start  = array_fill_keys($fields, 0);
        $end    = array_fill_keys($fields, 0);

        $total = count($groupItems);
        $index = 0;

        foreach ($groupItems as $item) {
            foreach ($fields as $key) {
                $current = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
                if ($current === $last[$key]) {
                    $end[$key] = $index;
                } else {
                    if ($last[$key] !== null) {
                        $result[$key][] = "{$start[$key]},{$end[$key]}";
                    }
                    $start[$key] = $index;
                    $end[$key]   = $index;
                    $last[$key]  = $item[$key];
                }

                // 最后一行
                if ($index === $total - 1) {
                    $result[$key][] = "{$start[$key]},{$end[$key]}";
                }
            }
            $index++;
        }

        return $result;
    }

    //获取页面渲染 Index序号=>count合并行数
    public function getIndexRowspan($fileds)
    {
        return collect($fileds)->map(function ($item) {
            return collect($item)->mapWithKeys(function ($i) {
                $arr = explode(',', $i);
                return [
                    $arr[0] => $arr[1]-$arr[0]+1,
                ];
            });
        })->toArray();
    }
    //废弃逻辑，保留，方便其他业务复制
    public function _getLogisticsItems($params)
    {
        $outbounds = $this->getModelClass()::query()
            ->where('customer_id', $params['customer_id'])
            ->where('clearance_id', $params['clearance_id'])
            ->where('payment_id', $params['payment_id'])
            ->where('outbound_at',$params['outbound_at'])
            ->get();
        $allItems = collect();

        foreach ($outbounds as $outbound) {
            $allItems = $allItems->merge($outbound->items);
        }

        $sortedItems = $allItems->sortBy([
            ['shipping_mark','asc'],
            ['carton_no_start', 'asc'],
            ['goods_id', 'asc'],
        ])->values();

        // ===========================================================================
        //  第一层：按 唛头-箱号区间 分组
        // ===========================================================================
        $grouped = $sortedItems->groupBy(function ($item) {
            if ($item->carton_no_start == $item->carton_no_end) {
                return $item->shipping_mark . '-' . $item->carton_no_start;
            } else {
                return $item->shipping_mark . '-' . $item->carton_no_start . '~' . $item->carton_no_end;
            }
        })->map(function ($items , $key) {
            return [
                'group_key' => $key,
                'group_rowspan' => count($items),
                'items' => $items
            ];
        });

        // ===========================================================================
        //  第二层：相同唛头内部 按照 商品|价格 分组
        // ===========================================================================

        $grouped = $grouped->map(function ($group) {
            $group_items = $group['items']
                ->groupBy(function ($i) {return $i->goods_id.'|'.$i->price;})->map(function ($subItems) {
                    $firstData = $subItems->first();
                    return [
                        'goods_id' => $firstData->goods_id,
                        'goods_name' => $firstData->goods->name,
                        'customer_sku' => $firstData->goods->customer_sku,
                        'main_image' => $firstData->goods->main_image,
                        'thumb_image' => $firstData->goods->thumb_image,
                        'price' => $firstData->price,
                        'quantity' => $subItems->sum('quantity'),
                        'amount' => $subItems->sum('amount'),
                        'color_text'     => $subItems->map(fn($i) =>
                            ($i->sku->color ?? '').' '.$i->quantity
                        )->implode(' '),
                    ];
                });
            $group['items'] = $group_items;
            return $group;
        });

        $flatList = collect();

        // 1. 先把所有合并后的商品打平成一维数组
        foreach ($grouped as $group) {
            foreach ($group['items'] as $item) {
                $flatList->push($item);
            }
        }

        // 2. 计算合并行数
        $lastGoodsId = null;
        $rowSpans = [];
        $spanCount = 1;
        foreach ($flatList as $key=>$item) {
            if ($item['goods_id'] === $lastGoodsId) {
                $spanCount++;
                $rowSpans[$key] = 0;
                if( $key === count($flatList) - 1 ) {
                    $rowSpans[$key-$spanCount+1] = $spanCount;
                }
            } else {
                $rowSpans[$key] = 1;
                if ($lastGoodsId !== null) {
                    $rowSpans[$key - $spanCount] = $spanCount;
                }
                $spanCount = 1;
                $lastGoodsId = $item['goods_id'];
            }
        }

        // 3. 把 goods_rowspan 回填到数组结构
        $index = 0;
        $grouped = $grouped->map(function ($group) use (&$index, $rowSpans) {
            $group['items'] = $group['items']->map(function ($item) use (&$index, $rowSpans) {
                $item['goods_rowspan'] = $rowSpans[$index] ?? 1;
                $index++;
                return $item;
            });
            return $group;
        });
        return $grouped;
    }

    public function store(array $data):bool
    {
        try {
            //防止重复提交
//            $lockKey = "outbound:submit:" . $data['outbound_code'];
//            if (!Cache::add($lockKey, 'locked', 60)) {
//                throw new \Exception('请勿重复提交', 429);
//            }

            DB::beginTransaction();
            $items = $data['goods'];
            unset($data['goods']);

            //创建总单
            $outbound = $this->getModelClass()::create($data);

            if (empty($items)) {
                throw new \Exception('至少添加一个产品', 400);
            }

            //创建子单
            $this->createOutboundItems($items,$outbound);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            //Cache::forget($lockKey);
            //dd($e->getMessage(), $e->getFile(), $e->getLine());
            throw new \Exception('新增失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(Model $model , array $data):bool
    {
        try {
            $items = $data['goods'];
            unset($data['goods']);
            unset($data['department_id'],$data['customer_id'],$data['outbound_code']);

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
                    $item['old_warehouse_id'] = $old['warehouse_id'];
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
                $this->createOutboundItems($newItems,$model);
            }

            //编辑数据
            if( !empty($updates) ){
                $this->updateOutboundItems($updates);
            }

            //删除数据
            if( !empty($oldItems) ){
                $this->deleteOutboundItems($oldItems);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            throw new \Exception('修改失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function createOutboundItems(array $items,Model $outbound)
    {
        // 保存子单
        foreach( $items as $item ) {
            unset($item['id']);
            $item['outbound_id'] = $outbound->id;
            $item['amount'] = bcmul($item['quantity'],$item['price'],2);
            $temp = bcmul($item['carton_length'], $item['carton_width'], 4);
            $item['cbm'] = bcmul($temp, $item['carton_height'], 2);
            OutboundItem::create($item);

            // 更新库存,库存增加值为出库的负数
            $this->updateGoodsSkuStock($item, $item['warehouse_id'] , -$item['quantity']);
        }
    }

    public function updateOutboundItems(array $items)
    {
        foreach( $items as $item ){
            $oldQuantity = $item['old_quantity'];//旧数量
            $quantityDiff = $item['quantity_diff'];
            $oldWarehouseId = $item['old_warehouse_id'];
            unset($item['old_quantity'],$item['quantity_diff'],$item['old_warehouse_id']);
            $item['amount'] = bcmul($item['quantity'],$item['price'],2);
            $temp = bcmul($item['carton_length'], $item['carton_width'], 4);
            $item['cbm'] = bcmul($temp, $item['carton_height'], 2);
            OutboundItem::where('id',$item['id'])->update($item);

            if( $oldWarehouseId == $item['warehouse_id'] ){
                //如果没更换仓库，库存就新增差值部分
                $this->updateGoodsSkuStock($item,$oldWarehouseId,-$quantityDiff);
            }else{
                //如果更换了仓库，原仓库扣除老数量，新仓库增加前端传过来的数量
                $this->updateGoodsSkuStock($item,$oldWarehouseId,$oldQuantity);
                $this->updateGoodsSkuStock($item,$item['warehouse_id'],-$item['quantity']);
            }

        }
    }

    public function deleteOutboundItems(array $items)
    {
        $deletes = [];
        foreach( $items as $item ){
            //增加库存中的数量
            $this->updateGoodsSkuStock($item,$item['warehouse_id'],$item['quantity']);
            $deletes[] = $item['id'];
        }
        if(!empty($deletes))  OutboundItem::destroy($deletes);
    }

    public function destroy(Model $model): bool
    {
        try {
            DB::beginTransaction();
            $items = $model->items->toArray();
            $this->deleteOutboundItems($items);
            $model->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('删除失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
