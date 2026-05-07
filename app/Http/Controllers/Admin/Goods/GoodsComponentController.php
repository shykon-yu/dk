<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsCategoryRequest;
use App\Http\Requests\Admin\GoodsComponentRequest;
use App\Models\GoodsComponent;
use App\Services\Admin\Goods\GoodsComponentService;
use Illuminate\Http\Request;

class GoodsComponentController extends Controller
{
    protected $goodsComponentService;
    public function __construct(GoodsComponentService $goodsComponentService)
    {
        $this->goodsComponentService = $goodsComponentService;

    }

    public function index(Request $request)
    {
        $params = $request->only('name','page');
        $list = $this->goodsComponentService->getGoodsComponentsList($params);
        return view('admin.goods.component.index', compact('list'));
    }

    public function create()
    {
        return view('admin.goods.component.create');
    }

    public function store(GoodsComponentRequest $request )
    {
        $this->goodsComponentService->store($request->only('name','name_en','name_kr','sort','status'));
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(GoodsComponent $component)
    {
        return view('admin.goods.component.edit', compact('component'));
    }

    public function update(GoodsComponent $component , GoodsComponentRequest $request)
    {
        $this->goodsComponentService->update($component,$request->only('name','name_en','name_kr','sort','status'));
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(GoodsComponent $component)
    {
        $this->goodsComponentService->destroy($component);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        $this->goodsComponentService->batchDestroy($ids);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功'
        ]);
    }

    public function status(Request $request , GoodsComponent $component)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        $component = $this->goodsComponentService->changeStatus($component, $request->status);
        return response()->json([
            'code'=>200,
            'status'=>$component->status,
            'msg' => '状态修改成功',
        ]);
    }
}
