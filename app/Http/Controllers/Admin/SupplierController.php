<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use App\Services\Admin\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    protected $supplierService;
    public function __construct(SupplierService $supplierService)
    {
        $this->middleware('permission:admin.suppliers.index')->only('index');
        $this->middleware('permission:admin.suppliers.store')->only('create', 'store');
        $this->middleware('permission:admin.suppliers.update')->only('edit', 'update','status');
        $this->middleware('permission:admin.suppliers.destroy')->only('destroy','batchDestroy');
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
        $data = array_merge($request->validated(), ['supplier_category_id' => $supplier_category_id]);
        $this->supplierService->store($data);
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        return view('admin.supplier.edit', compact('supplier'));
    }

    public function update(Supplier $supplier , SupplierRequest $request)
    {
        $this->authorize('update', $supplier);
        $this->supplierService->update($supplier,$request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('destroy', $supplier);
        $this->supplierService->destroy($supplier);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        $this->supplierService->batchDestroy($ids);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功'
        ]);
    }

    public function status(Request $request , Supplier  $supplier)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        $supplier = $this->supplierService->changeStatus($supplier, $request->status);
        return response()->json([
            'code'=>200,
            'status'=>$supplier->status,
            'msg' => '状态修改成功',
        ]);
    }
}
