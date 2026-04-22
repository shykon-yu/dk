<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsCategoryService;
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
}
