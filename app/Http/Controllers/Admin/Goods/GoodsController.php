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
        try{
            $this->goodsService->store($request->all());
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
    public function edit(Goods $good)
    {
        $warehouses = app(WarehouseService::class)->getWarehouseByDepartmentId($good->department_id);
        $customers = app(CustomerService::class)->getCustomerByDepartmentId($good->department_id);
        $categoryChildren = app(GoodsCategoryService::class)->getChildrenByParentId($good->category->parent_id);
        return view('admin.goods.edit', compact('good','customers','warehouses','categoryChildren'));
    }

    public function update(GoodsRequest $request, Goods $good)
    {
        try{
            $this->goodsService->update($good,$request->all());
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

    public function destroy(Goods $good)
    {
        try{
            $this->goodsService->destroy($good);
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
            $this->goodsService->batchDestroy($ids);
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

    public function status(Request $request , GoodsCategory $category)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        try{
            $category = $this->goodsCategoryService->changeStatus($category, $request->status);
            return response()->json([
                'code'=>200,
                'status'=>$category->status,
                'msg' => '状态修改成功',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code'=>500,
                'msg'=>$e->getMessage(),
            ]);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $file = $request->file('file');
            // 调用基类的上传方法，指定模块为goods
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
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
