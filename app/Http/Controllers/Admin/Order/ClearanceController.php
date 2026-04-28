<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClearanceRequest;
use App\Models\Clearance;
use App\Services\Admin\ClearanceService;
use Illuminate\Http\Request;

class ClearanceController extends Controller
{
    protected $clearanceService;
    public function __construct(ClearanceService $clearanceService)
    {
        $this->middleware('permission:admin.clearances.index')->only('index');
        $this->middleware('permission:admin.clearances.store')->only('create', 'store');
        $this->middleware('permission:admin.clearances.update')->only('edit', 'update','status');
        $this->middleware('permission:admin.clearances.destroy')->only('destroy','batchDestroy');
        $this->clearanceService = $clearanceService;

    }

    public function index(Request $request)
    {
        $list = $this->clearanceService->getClearancesList($request->all());
        return view('admin.clearance.index', compact('list'));
    }

    public function create()
    {
        return view('admin.clearance.create');
    }

    public function store( ClearanceRequest $request )
    {
        try{
            $this->clearanceService->store($request->all());
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
    public function edit(Clearance $clearance)
    {
        return view('admin.clearance.edit', compact('clearance'));
    }

    public function update(Clearance $clearance , ClearanceRequest $request)
    {
        try{
            $this->clearanceService->update($clearance,$request->all());
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

    public function destroy(Clearance $clearance)
    {
        try{
            $this->clearanceService->destroy($clearance);
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
            $this->clearanceService->batchDestroy($ids);
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

    public function status(Request $request , Clearance $clearance)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $clearance = $this->clearanceService->changeStatus($clearance, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$clearance->status,
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
