<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 用户列表页
     */
    public function index(Request $request)
    {
        $user_list = User::query()
            ->when($request->user_name, function ($q) use ($request) {
                $q->where('user_name', 'like', "%{$request->user_name}%");
            })
            ->when($request->name, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->name}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.user.index', compact('user_list'));
    }

    /**
     * 删除用户（单条 + 批量）
     * 标准规范：加异常捕获
     */
    public function delete(Request $request)
    {
        try {
            $ids = explode(',', $request->user_ids);

            // 安全：禁止删除自己
            if (in_array(auth()->id(), $ids)) {
                return response()->json([
                    'code' => 403,
                    'msg' => '不能删除自己'
                ]);
            }

            User::destroy($ids);

            return response()->json([
                'code' => 200,
                'msg' => '删除成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'msg' => '删除失败：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 编辑页面
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * 详情页面
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.show', compact('user'));
    }
}
