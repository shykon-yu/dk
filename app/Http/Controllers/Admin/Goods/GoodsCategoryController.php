<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsCategoryRequest;
use App\Models\GoodsCategory;
use App\Services\Admin\Goods\GoodsCategoryService;
use Illuminate\Http\Request;

class GoodsCategoryController extends Controller
{
    protected $goodsCategoryService;
    public function __construct(GoodsCategoryService $goodsCategoryService)
    {
        $this->goodsCategoryService = $goodsCategoryService;

    }

    public function index(Request $request)
    {
        $params = $request->only('name','page');
        $list = $this->goodsCategoryService->getGoodsCategoriesList($params);
        return view('admin.goods.category.index', compact('list'));
    }

    public function create()
    {
        $parentCategories = $this->goodsCategoryService->getTopLevel();
        return view('admin.goods.category.create', compact('parentCategories'));
    }

    public function store(GoodsCategoryRequest $request )
    {
        $level = $request->input('parent_id')==0?1:2;
        $data = array_merge($request->only('name','parent_id','sort','status'), ['level' => $level]);
        $this->goodsCategoryService->store($data);
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }
    public function edit(GoodsCategory $category)
    {
        $parentCategories = $this->goodsCategoryService->getTopLevel();
        return view('admin.goods.category.edit', compact('category', 'parentCategories'));
    }

    public function update(GoodsCategoryRequest $request, GoodsCategory $category)
    {
        $level = $request->input('parent_id')==0?1:2;
        $data = array_merge($request->only('name','parent_id','sort','status'), ['level' => $level]);
        $this->goodsCategoryService->update($category,$data);
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(GoodsCategory $category)
    {
        $this->goodsCategoryService->destroy($category);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        $this->goodsCategoryService->batchDestroy($ids);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功'
        ]);
    }

    public function status(Request $request , GoodsCategory $category)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        $category = $this->goodsCategoryService->changeStatus($category, $request->status);
        return response()->json([
            'code'=>200,
            'status'=>$category->status,
            'msg' => '状态修改成功',
        ]);
    }
}
