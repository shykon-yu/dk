<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use App\Services\Admin\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;
    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;

    }

    public function index(Request $request)
    {
        $params = $request->only('name','page');
        $list = $this->supplierService->getCSupplierssList($params);
        return view('admin.supplier.index', compact('list'));
    }

    public function create()
    {
        return view('admin.supplier.create');
    }

    public function store( SupplierRequest $request )
    {
        $supplier_category_id = 1;

        $data = array_merge($request->except('_token'), ['supplier_category_id' => $supplier_category_id]);
        try{
            $this->supplierService->store($data);
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
    public function edit(Supplier $supplier)
    {
        return view('admin.supplier.edit', compact('supplier'));
    }

    public function update(Supplier $supplier , SupplierRequest $request)
    {
        try{
            $this->supplierService->update($supplier,$request->all());
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

    public function destroy(Supplier $supplier)
    {
        try{
            $this->supplierService->destroy($supplier);
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
            $this->supplierService->batchDestroy($ids);
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

    public function status(Request $request , Supplier  $supplier)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $supplier = $this->supplierService->changeStatus($supplier, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$supplier->status,
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
