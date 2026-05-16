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
        $this->middleware('permission:admin.outbounds.index')->only('index');
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

    public function store( OutboundRequest $request )
    {
        $this->outboundService->store($request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(Inbound $inbound)
    {
        $this->authorize('update', $inbound);
        $params = ['customer_id' => $inbound->customer_id,];
        $goods = app(GoodsService::class)->search($params);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($inbound->department_id);
        $warehouses = app(ViewDataService::class)->getWarehouses();
        $warehouses = $warehouses->where('department_id', $inbound->department_id)->values();
        return view('admin.order.inbound.edit', compact('inbound', 'customers', 'goods', 'warehouses'));
    }

    public function show(Inbound $inbound)
    {
        $this->authorize('update', $inbound);
        $params = ['customer_id' => $inbound->customer_id,];
        $goods = app(GoodsService::class)->search($params);
        $customers = app(ViewDataService::class)->getCustomers();
        $customers = $customers->where('department_id',$inbound->department_id)->values();
        $warehouses = app(ViewDataService::class)->getWarehouses();
        $warehouses = $warehouses->where('department_id', $inbound->department_id)->values();
        return view('admin.order.inbound.show', compact('inbound', 'customers', 'goods', 'warehouses'));
    }

    public function update(InboundRequest $request, Inbound $inbound)
    {
        $this->inboundService->update($inbound,$request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(Inbound $inbound)
    {
        $this->authorize('delete', $inbound);
        $this->inboundService->destroy($inbound);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function status(Request $request , Order $order)
    {
        $request->validate(['status'=>['required','integer','between:0,3']]);
        $order = $this->orderService->changeStatus($order, $request->status);
        return response()->json([
            'code'=>200,
            'status'=>$order->status,
            'msg' => '状态修改成功',
        ]);
    }

    public function star(Request $request , Order $order)
    {
        $request->validate(['star'=>['required','integer','between:0,1']]);
        $order = $this->orderService->changeStar($order, $request->star);
        return response()->json([
            'code'=>200,
            'star'=>$order->is_star,
            'msg' => '状态修改成功',
        ]);
    }

    public function uploadExcel(Request $request)
    {
        $file = $request->file('file');
        $uploadResult = $this->orderService->uploadExcel($file);
        return response()->json([
            'code' => 200,
            'msg' => '上传成功',
            'data' => [
                'id' => $uploadResult['id'],
                'name' => $uploadResult['name']
            ]
        ]);
    }
}
