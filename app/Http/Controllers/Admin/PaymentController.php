<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\Payment;
use App\Services\Admin\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('permission:admin.payments.index')->only('index');
        $this->middleware('permission:admin.payments.store')->only('create', 'store');
        $this->middleware('permission:admin.payments.update')->only('edit', 'update','status');
        $this->middleware('permission:admin.payments.destroy')->only('destroy','batchDestroy');
        $this->paymentService = $paymentService;

    }

    public function index(Request $request)
    {
        $list = $this->paymentService->getPaymentsList($request->all());
        return view('admin.payment.index', compact('list'));
    }

    public function create()
    {
        return view('admin.payment.create');
    }

    public function store( PaymentRequest $request )
    {
        try{
            $this->paymentService->store($request->all());
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
    public function edit(Payment $payment)
    {
        return view('admin.payment.edit', compact('payment'));
    }

    public function update(Payment $payment , PaymentRequest $request)
    {
        try{
            $this->paymentService->update($payment,$request->all());
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

    public function destroy(Payment $payment)
    {
        try{
            $this->paymentService->destroy($payment);
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
            $this->paymentService->batchDestroy($ids);
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

    public function status(Request $request , Payment $payment)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $payment = $this->paymentService->changeStatus($payment, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$payment->status,
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
