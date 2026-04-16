<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Department;
use App\Services\Admin\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;

    }

    public function index(Request $request)
    {
        $params = $request->only('name','page');
        $list = $this->customerService->getCustomersList($params);
        return view('admin.customer.index', compact('list'));
    }

    public function create()
    {
        return view('admin.department.create');
    }

    public function store( DepartmentRequest $request )
    {
        try{
            $this->departmentService->store($request->only('name','status'));
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
    public function edit(Department $department)
    {
        return view('admin.department.edit', compact('department'));
    }

    public function update(Department $department , DepartmentRequest $request)
    {
        try{
            $this->departmentService->update($department,$request->only('name','status'));
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

    public function destroy(Department $department)
    {
        try{
            $this->departmentService->destroy($department);
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
            $this->departmentService->batchDestroy($ids);
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

    public function status(Request $request , Department $department)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $department = $this->departmentService->changeStatus($department, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$department->status,
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
