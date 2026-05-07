<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsSeasonRequest;
use App\Models\GoodsSeason;
use App\Enums\GoodsSeasonEnum;
use App\Services\Admin\Goods\GoodsSeasonService;
use Illuminate\Http\Request;

class GoodsSeasonController extends Controller
{
    protected $goodsSeason;
    public function __construct(GoodsSeasonService $goodsSeason)
    {
        $this->goodsSeason = $goodsSeason;
    }

    public function index(Request $request , GoodsSeason $goodsSeason)
    {
        $params = $request->only('year','name','season','page');
        $years = $this->goodsSeason->getYearsOptions();
        $seasons = GoodsSeasonEnum::getOptions();
        $goodsSeasonsList = $this->goodsSeason->getGoodsSeasonsList($params);
        return view('admin.goods.season.index', compact('years','seasons','goodsSeasonsList'));
    }

    public function create()
    {
        $seasons = GoodsSeasonEnum::getOptions();
        return view('admin.goods.season.create', compact('seasons'));
    }

    public function store(GoodsSeasonRequest $request )
    {
        $this->goodsSeason->store($request->only('year','name','season','status'));
        return response()->json([
            'code' => 200,
            'msg' => '新增成功',
        ]);
    }

    public function edit(GoodsSeason $season)
    {
        return view('admin.goods.season.edit', compact('season'));
    }

    public function update(Request $request, GoodsSeason $season)
    {
        $this->goodsSeason->update($season,$request->only('year','name','season','status'));
        return response()->json([
            'code' => 200,
            'msg' => '修改成功'
        ]);
    }

    public function destroy(GoodsSeason $season)
    {
        $this->goodsSeason->destroy($season);
        return response()->json([
            'code' => 200,
            'msg' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids',[]);
        $this->goodsSeason->batchDestroy($ids);
        return response()->json([
           'code' => 200,
           'msg' => '删除成功'
        ]);
    }

    public function status(Request $request , GoodsSeason $season)
    {
        $request->validate(['status'=>['required','integer','between:0,1']]);
        $season = $this->goodsSeason->changeStatus($season, $request->status);
        return response()->json([
           'code'=>200,
           'status'=>$season->status,
           'msg' => '状态修改成功',
        ]);
    }

    public function current(Request $request , GoodsSeason $season)
    {
        $request->validate(['current'=>['required','integer','between:0,1']]);
        $season = $this->goodsSeason->changeCurrent($season, $request->current);
        return response()->json([
            'code'=>200,
            'is_current'=>$season->is_current,
            'msg' => '当季修改成功',
        ]);
    }

}
