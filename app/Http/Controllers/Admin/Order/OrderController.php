<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Models\Order;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->middleware('permission:admin.orders.index')->only('index');
        $this->middleware('permission:admin.orders.store')->only('create', 'store');
        $this->middleware('permission:admin.orders.update')->only('edit', 'update','status','star');
        $this->middleware('permission:admin.orders.destroy')->only('destroy','batchDestroy');
        $this->orderService = $orderService;

    }

    public function index(Request $request)
    {
        $mainOrders = $this->orderService->getOrdersList($request->all());
        return view('admin.order.index', compact('mainOrders'));
    }

    public function create()
    {
        return view('admin.order.create');
    }

    public function store( OrderRequest $request )
    {
        $this->orderService->store($request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(Order $order)
    {
        $params = ['customer_id' => $order->customer_id,];
        $goods = app(GoodsService::class)->search($params);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($order->department_id);
        return view('admin.order.edit', compact('order', 'customers', 'goods'));
    }

    public function update(OrderRequest $request, Order $order)
    {
        $this->orderService->update($order,$request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(Order $order)
    {
        $this->orderService->destroy($order);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function status(Request $request , Order $order)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
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
