<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserMigrationController extends Controller
{
    protected $chunkSize = 200;// 每次插200条，避免数据包太大
    //将老users数据表修改成新表样式集合，存入users表里
    public function migrate()
    {
        $this->migrateMenu();
    }

    public function migrateMenu()
    {
        $menuData = DB::table('dk_column')->get();
        //dd($menuData->toArray());
        $insertData = [];
        foreach ($menuData as $menu) {
            $insertData[] = [
                'id' => $menu->id,
                'title' => $menu->column,
                'status' => $menu->status,
                'url' => $menu->url,
                'parent_id' => $menu->pid,
                'index_' => $menu->index_,
                'p_index' => max((int)$menu->p_index, 0),
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        //dd($insertData);
        try{
            DB::beginTransaction();
            foreach (array_chunk($insertData, $this->chunkSize) as $chunk) {
                DB::table('menus')->insert($chunk);
            }
            DB::commit();
            dd('执行完成');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception->getMessage());
        }
    }
    //存入中间表数据
    public function insertUserDpt()
    {
        $usersData = User::all();
        $inserData = [];
        foreach ($usersData as $user) {
            $departmentArr = explode(',', $user->departments);

            foreach( $departmentArr as $department_id ){
                $inserData[] = [
                    'user_id' => $user->id,
                    'department_id' => $department_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        try{
            DB::beginTransaction();
            DB::table('user_department')->insert($inserData);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception->getMessage());
        }
        //dd($inserData);
    }
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

        foreach (array_chunk($userData, $this->chunkSize) as $chunk) {
            User::insert($chunk);
        }

        $total = count($userData);
        return "批量迁移完成！共导入 {$total} 条用户！";
    }

    public function migrateDkDptToDpt()
    {
        $oldDpts = DB::table('dk_department')->get();
        $departmentData = [];
        foreach( $oldDpts as $oldDpt ) {
            if( $oldDpt->status == 1 ) {
                $departmentData[] = [
                    'name' => $oldDpt->department_name,
                    'status' => $oldDpt->status,
                    'old_id' => $oldDpt->id,
                ];
            }
        }
        //dd($departmentData);
        foreach( array_chunk($departmentData, $this->chunkSize) as $chunk ) {
            Department::insert($chunk);
        }
        $total = count( $departmentData );
        return "批量迁移完成！共导入 {$total} 条数据！";
    }

    public function migrateUpdateUsers()
    {
        $oldUsers = DB::table('dk_users')->get();
        $userData = [];

        foreach( $oldUsers as $oldUser ) {
            $userData[$oldUser->user_name] = [
                'old_id' => $oldUser->id,
                'departments' => $oldUser->departments,
            ];
        }

        try {
            DB::beginTransaction();

            foreach ($userData as $username => $value) {
                // ✅ 强制转字符串，过滤NULL，解决MySQL隐式转换报错
                $updateData = array_filter($value);
                $updateData['departments'] = implode(',', explode(',', $value['departments']));
                $user_data = User::where('username', $username)->get()->first();
                //dd($user_data->id);
                User::where('id', $user_data->id)->update($updateData);
            }

            DB::commit();
            //return back()->with('success', '批量更新成功');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage()); // 开发环境调试
            // return back()->with('error', $e->getMessage());
        }
    }
}
