<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WarehouseRequest;
use App\Models\Warehouse;
use App\Services\Admin\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected $warehouseService;
    public function __construct(WarehouseService $warehouseService)
    {
        $this->middleware('permission:admin.warehouses.index')->only('index');
        $this->middleware('permission:admin.warehouses.store')->only('create', 'store');
        $this->middleware('permission:admin.warehouses.update')->only('edit', 'update','status');
        $this->middleware('permission:admin.warehouses.destroy')->only('destroy','batchDestroy');
        $this->warehouseService = $warehouseService;

    }

    public function index(Request $request)
    {
        $params = $request->only('name','department_ids','page');
        $list = $this->warehouseService->getWarehousesList($params);
        return view('admin.warehouse.index', compact('list'));
    }

    public function create()
    {
        return view('admin.warehouse.create');
    }

    public function store( WarehouseRequest $request )
    {
        try{
            $this->warehouseService->store($request->all());
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
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouse.edit', compact('warehouse'));
    }

    public function update(Warehouse $warehouse , WarehouseRequest $request)
    {
        try{
            $this->warehouseService->update($warehouse,$request->all());
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

    public function destroy(Warehouse $warehouse)
    {
        try{
            $this->warehouseService->destroy($warehouse);
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
            $this->warehouseService->batchDestroy($ids);
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

    public function status(Request $request , Warehouse $warehouse)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $warehouse = $this->warehouseService->changeStatus($warehouse, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$warehouse->status,
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
