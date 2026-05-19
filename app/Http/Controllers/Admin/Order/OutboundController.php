<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OutboundRequest;
use App\Models\Order;
use App\Models\Outbound;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Order\OutboundService;
use App\Services\Admin\ViewDataService;
use Illuminate\Http\Request;

class OutboundController extends Controller
{
    protected $outboundService;
    public function __construct(OutboundService $outboundService)
    {
        $this->middleware('permission:admin.outbounds.index')->only('index','show','logisticsIndex');
        $this->middleware('permission:admin.outbounds.store')->only('create', 'store');
        $this->middleware('permission:admin.outbounds.update')->only('edit', 'update','status','star');
        $this->middleware('permission:admin.outbounds.destroy')->only('destroy','batchDestroy');
        $this->outboundService = $outboundService;

    }

    public function index(Request $request)
    {
        $mainOrders = $this->outboundService->getOutboundsList($request->all());
        return view('admin.order.outbound.index', compact('mainOrders'));
    }

    public function items(Request $request)
    {
        $items = $this->outboundService->getItems($request->all());
        return view('admin.order.outbound.items', compact('items'));
    }

    public function create()
    {
        return view('admin.order.outbound.create');
    }

    public function store(OutboundRequest $request )
    {
        $this->outboundService->store($request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(Outbound $outbound)
    {
        $this->authorize('update', $outbound);
        // 预加载子单 + 商品 + SKU + 库存
        $outbound->load([
            'items.goods',
            'items.sku',
        ]);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($outbound->department_id);
        $warehouses = app(ViewDataService::class)->getWarehouses();
        $warehouses = $warehouses->where('department_id', $outbound->department_id)->values();
        return view('admin.order.outbound.edit', compact('outbound', 'customers', 'warehouses'));
    }

    public function show(Outbound $outbound)
    {
        // 预加载子单 + 商品 + SKU + 库存
        $outbound->load([
            'items.goods',
            'items.sku',
        ]);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($outbound->department_id);
        $warehouses = app(ViewDataService::class)->getWarehouses();
        $warehouses = $warehouses->where('department_id', $outbound->department_id)->values();
        return view('admin.order.outbound.show', compact('outbound', 'customers', 'warehouses'));
    }

    public function update(OutboundRequest $request, Outbound $outbound)
    {
        $this->outboundService->update($outbound,$request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(Outbound $outbound)
    {
        $this->authorize('delete', $outbound);
        $this->outboundService->destroy($outbound);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    //物流订单页面
    public function logisticsIndex(Request $request)
    {
        $mainOrders = $this->outboundService->getOutboundsList($request->all(),true);
        return view('admin.order.outbound.logistics.index', compact('mainOrders'));
    }

    //物流详情页面
    public function logisticsShow($order_at,$customer_id,$clearance_id,$payment_id)
    {
        $params = [
            'customer_id' => $customer_id,
            'clearance_id' => $clearance_id,
            'payment_id' => $payment_id,
            'outbound_at' => $order_at,
        ];
        $items = $this->outboundService->getLogisticsItems($params);
        $fields = ['shipping_mark_text','goods_id'];//传入需要合并的字段
        $fieldArray = $this->outboundService->getFieldMerge($items, $fields);//获取合并字段坐标
        $indexRowspan = $this->outboundService->getIndexRowspan($fieldArray);//转换成页面需要的序号=>合并行数
        return view('admin.order.outbound.logistics.show', compact('items', 'indexRowspan'));
    }
}
