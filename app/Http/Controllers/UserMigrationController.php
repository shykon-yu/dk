<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserMigrationController extends Controller
{
    //将老users数据表修改成新表样式集合，存入users表里
    public function migrateDkUsersToUsers()
    {
        // 1. 获取所有老数据
        $oldUsers = DB::table('dk_users')->get();
        // 2. 定义空数组，批量收集数据
        $userData = [];

        foreach ($oldUsers as $old) {
            // 组装新表结构
            $userData[] = [
                'username'      => $old->user_name,
                'name'          => $old->name,
                'email'         => $old->email,
                'password'      => $old->password,
                'phone_number'  => $old->phone_number,
                'section_id'    => $old->section_id,
                'role_id'       => $old->group_id ?? 0,
                'status'        => $old->status,
                'open_id'       => $old->open_id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        // 🔥 🔥 🔥 批量一次性插入（核心！只执行一次SQL）
        $chunkSize = 200; // 每次插200条，避免数据包太大
        foreach (array_chunk($userData, $chunkSize) as $chunk) {
            User::insert($chunk);
        }

        $total = count($userData);
        return "批量迁移完成！共导入 {$total} 条用户！";
    }
}
