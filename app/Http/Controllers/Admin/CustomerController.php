<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Customer;
use App\Services\Admin\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;
    public function __construct(CustomerService $customerService)
    {
        $this->middleware('permission:admin.customers.index')->only('index');
        $this->middleware('permission:admin.customers.store')->only('create', 'store');
        $this->middleware('permission:admin.customers.update')->only('edit', 'update','status');
        $this->middleware('permission:admin.customers.destroy')->only('destroy','batchDestroy');
        $this->customerService = $customerService;

    }

    public function index(Request $request)
    {
        $list = $this->customerService->getCustomersList($request->all());
        return view('admin.customer.index', compact('list'));
    }

    public function create()
    {
        return view('admin.customer.create');
    }

    public function store( DepartmentRequest $request )
    {
        try{
            $this->customerService->store($request->all());
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
    public function edit(Customer $customer)
    {
        return view('admin.customer.edit', compact('customer'));
    }

    public function update(Customer $customer , CustomerRequest $request)
    {
        try{
            $this->customerService->update($customer,$request->all());
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

    public function destroy(Customer $customer)
    {
        try{
            $this->customerService->destroy($customer);
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
            $this->customerService->batchDestroy($ids);
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

    public function status(Request $request , Customer $customer)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $customer = $this->customerService->changeStatus($customer, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$customer->status,
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
