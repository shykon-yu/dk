<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsCategoryService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Goods\GoodsSkuService;
use App\Services\Admin\Goods\GoodsSkuStockService;
use App\Services\Admin\Order\OrderService;
use App\Services\Admin\ViewDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    protected $viewData;

    public function __construct(ViewDataService $viewData)
    {
        $this->viewData = $viewData;
    }

    public function getCustomerByDept(Request $request): JsonResponse
    {
        $deptId = $request->department_id;
        $customers = $deptId?app(CustomerService::class)->getCustomerByDepartmentId($deptId):collect();
        return response()->json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $customers
        ]);
    }

    // 根据部门获取仓库
    public function getWarehouseByDept(Request $request): JsonResponse
    {
        $deptId = $request->department_id;
        $warehouses = $this->viewData->getWarehouses();
        if ($deptId) {
            $warehouses = $warehouses->where('department_id', $deptId)->values();
        }
        return response()->json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $warehouses
        ]);
    }

    public function getCategoryByParent(Request $request)
    {
        $pid = $request->pid;
        $children = $pid?app(GoodsCategoryService::class)->getChildrenByParentId($pid):collect();
        return response()->json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $children
        ]);
    }

    public function getGoodsSearch(Request $request)
    {
        $request->validate(['customer_id'=>['required','integer','exists:customers,id']]);
        $customer_id = $request->customer_id;
        $keyword = $request->keyword;
        $params = [
            'customer_id' => $customer_id,
            'keyword' => $keyword
        ];
        $goods = app(GoodsService::class)->search($params);
        return response()->json([
            'code' => 200,
            'data' => $goods
        ]);
    }

    //获取所选客户默认商品
    public function getCustomerDefaultGoods(Request $request)
    {
        $request->validate(['customer_id'=>['required','integer','exists:customers,id']]);
        $params = [
            'customer_id' => $request->customer_id,
        ];
        $goods = app(GoodsService::class)->search($params);
        return response()->json([
            'code' => 200,
            'data' => $goods
        ]);
    }

    public function getCustomerGoodsWithStock(Request $request)
    {
        $request->validate([
            'customer_id'=>['required','integer','exists:customers,id'],
            'warehouse_id'=>['required','integer','exists:warehouses,id'],
        ]);
        $params = [
            'customer_id' => $request->customer_id,
            'warehouse_id' => $request->warehouse_id,
        ];
        $goods = app(GoodsService::class)->getCustomerGoodsWithStock($params);
        return response()->json([
            'code' => 200,
            'data' => $goods
        ]);
    }

    //通过商品获取sku
    public function getSkuByGoods(Request $request)
    {
        $request->validate(
            [
                'goods_id'=>['required','integer','exists:goods,id'],
                'warehouse_id'=>['nullable','integer','exists:warehouses,id'],
            ]
        );
        $params = [
            'goods_id' => $request->goods_id,
            'warehouse_id' => $request->warehouse_id??null,
        ];
        $skus = app(GoodsSkuService::class)->getSkus($params);
        $goods = $skus->first()->goods;
//        dd($data);
//        $goods = app(GoodsService::class)->getGoodsInfo($request->goods_id);
//        $skus = $goods->skus;
        return response()->json([
            'code' => 200,
            'data' => [
                'goods' => $goods,
                'skus' => $skus
            ]
        ]);
    }

    public function getOrderItemsList(Request $request)
    {
        $items = app(OrderService::class)->getOrderItems($request->all());
        return response()->json([
            'code' => 200,
            'list' => $items
        ]);
    }

    //获取库存
    public function getStockInfo(Request $request)
    {
        $request->validate([
            'sku_id'=>['required','integer','exists:skus,id'],
            'warehouse_id'=>['required','integer','exists:warehouses,id'],
        ]);
        $params = [
            'sku_id' => $request->sku_id,
            'warehouse_id' => $request->warehouse_id,
        ];
        $data = app(GoodsSkuStockService::class)->getStockInfo($params);

        return response()->json([
            'code' => 200,
            'data' => $data
        ]);
    }
}
