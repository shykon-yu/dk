<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsRequest;
use App\Models\Goods;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsCategoryService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Order\OrderService;
use App\Services\Admin\WarehouseService;
use Illuminate\Http\Request;
use App\Enums\OrderStatusEnum;

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
        $goods = app(GoodsService::class)->getCurrentGoodsList();
        return view('admin.order.create', compact('goods'));
    }

    public function store(GoodsRequest $request )
    {
        try{
            $this->goodsService->store($request->all());
            return response()->json([
                'code' => 200,
                'msg' => '新增成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }
    public function edit(Goods $good)
    {
        $warehouses = app(WarehouseService::class)->getWarehouseByDepartmentId($good->department_id);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($good->department_id);
        $categoryChildren = app(GoodsCategoryService::class)->getChildrenByParentId($good->category->parent_id);
        return view('admin.goods.edit', compact('good','customers','warehouses','categoryChildren'));
    }

    public function update(GoodsRequest $request, Goods $good)
    {
        try{
            $this->goodsService->update($good,$request->all());
            return response()->json([
                'code' => 200,
                'msg' => '修改成功'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(Goods $good)
    {
        try{
            $this->goodsService->destroy($good);
            return response()->json([
                'code' => 200,
                'msg' => '删除成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        if(empty($ids)){
            return response()->json([
                'code' => 400,
                'msg' => '请选择',
            ]);
        }
        try{
            $this->goodsService->batchDestroy($ids);
            return response()->json([
                'code' => 200,
                'msg' => '删除成功'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function status(Request $request , Goods $good)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $good = $this->goodsService->changeStatus($good, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$good->status,
                'msg' => '状态修改成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code'=>500,
                'msg'=>$e->getMessage(),
            ]);
        }
    }

    public function star(Request $request , Goods $good)
    {
        $request->validate(['star'=>['required','integer','between:0,1']]);
        try{
            $good = $this->goodsService->changeStar($good, $request->star);
            return response()->json([
                'code'=>200,
                'star'=>$good->is_star,
                'msg' => '状态修改成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code'=>500,
                'msg'=>$e->getMessage(),
            ]);
        }
    }
}
