<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InboundRequest;
use App\Http\Requests\Admin\OrderRequest;
use App\Models\Inbound;
use App\Models\Order;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Order\InboundService;
use App\Services\Admin\ViewDataService;
use Illuminate\Http\Request;

class InboundController extends Controller
{
    protected $inboundService;
    public function __construct(InboundService $inboundService)
    {
        $this->middleware('permission:admin.inbounds.index')->only('index');
        $this->middleware('permission:admin.inbounds.store')->only('create', 'store');
        $this->middleware('permission:admin.inbounds.update')->only('edit', 'update','status','star');
        $this->middleware('permission:admin.inbounds.destroy')->only('destroy','batchDestroy');
        $this->inboundService = $inboundService;

    }

    public function index(Request $request)
    {
        $mainOrders = $this->inboundService->getInboundsList($request->all());
        return view('admin.order.inbound.index', compact('mainOrders'));
    }

    public function items(Request $request)
    {
        $items = $this->inboundService->getItems($request->all());
        return view('admin.order.inbound.items', compact('items'));
    }

    public function create()
    {
        return view('admin.order.inbound.create');
    }

    public function store( InboundRequest $request )
    {
        $this->inboundService->store($request->validated());
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
