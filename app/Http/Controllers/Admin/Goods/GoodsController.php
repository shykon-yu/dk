<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsRequest;
use App\Models\Goods;
use App\Services\Admin\CustomerService;
use App\Services\Admin\Goods\GoodsCategoryService;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GoodsController extends Controller
{
    protected $goodsService;
    public function __construct(GoodsService $goodsService)
    {
        $this->middleware('permission:admin.goods.index')->only('index');
        $this->middleware('permission:admin.goods.store')->only('create', 'store');
        $this->middleware('permission:admin.goods.update')->only('edit', 'update','status','star');
        $this->middleware('permission:admin.goods.destroy')->only('destroy','batchDestroy');
        $this->goodsService = $goodsService;

    }

    public function index(Request $request)
    {
        $list = $this->goodsService->getGoodsList($request->all());
        return view('admin.goods.index', compact('list'));
    }

    public function create()
    {
        return view('admin.goods.create');
    }

    public function store(GoodsRequest $request )
    {
        $this->goodsService->store($request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(Goods $good)
    {
        $warehouses = app(WarehouseService::class)->getWarehouseByDepartmentId($good->department_id);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($good->department_id);
        $categoryChildren = app(GoodsCategoryService::class)->getChildrenByParentId($good->category->parent_id);
        return view('admin.goods.edit', compact('good','customers','warehouses','categoryChildren'));
    }

    public function update(GoodsRequest $request, Goods $good)
    {
        $this->goodsService->update($good,$request->validated());
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(Goods $good)
    {
        $this->goodsService->destroy($good);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        $this->goodsService->batchDestroy($ids);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功'
        ]);
    }

    public function status(Request $request , Goods $good)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        $good = $this->goodsService->changeStatus($good, $request->status);
        return response()->json([
            'code'=>200,
            'status'=>$good->status,
            'msg' => '状态修改成功',
        ]);
    }

    public function star(Request $request , Goods $good)
    {
        $request->validate(['star'=>['required','integer','between:0,1']]);
        $good = $this->goodsService->changeStar($good, $request->star);
        return response()->json([
            'code'=>200,
            'star'=>$good->is_star,
            'msg' => '状态修改成功',
        ]);
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('file');
        $uploadResult = $this->goodsService->uploadImage($file, 'goods');

        return response()->json([
            'code' => 200,
            'msg' => '上传成功',
            'data' => [
                'id' => $uploadResult['id'],
                'url' => $uploadResult['main_url'],
                'thumb_url' => $uploadResult['thumb_url']
            ]
        ]);
    }
}
