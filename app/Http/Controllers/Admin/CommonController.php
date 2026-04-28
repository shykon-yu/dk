<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsCategoryService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Goods\GoodsSkuService;
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
        $customer_id = $request->customer_id;
        $keyword = $request->keyword;
        $params = [
            'customer_id' => $customer_id,
            'keyword' => $keyword
        ];
        try{
            $goods = app(GoodsService::class)->search($params);
            return response()->json([
                'code' => 200,
                'data' => $goods
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    //获取所选客户默认商品
    public function getCustomerDefaultGoods(Request $request)
    {
        $customer_id = $request->customer_id;
        $params = [
            'customer_id' => $customer_id,
        ];
        try{
            $goods = app(GoodsService::class)->search($params);
            return response()->json([
                'code' => 200,
                'data' => $goods
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    //通过商品获取sku
    public function getSkuByGoods(Request $request)
    {
        try{
            $goods = app(GoodsService::class)->getGoodsInfo($request->goods_id);
            $skus = $goods->skus;
            return response()->json([
                'code' => 200,
                'data' => [
                    'goods' => $goods,
                    'skus' => $skus
                ]
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
