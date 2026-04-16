<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserMigrationController extends Controller
{
    protected $chunkSize = 200;// 每次插200条，避免数据包太大
    //将老users数据表修改成新表样式集合，存入users表里
    public function migrate()
    {
        $this->migrateCustom();
    }

    public function migrateCustom(){
        $oldData = DB::table("dk_supplier_company")->where('status',1)->get();
        $data = [];
        foreach ($oldData as $item){
            $data[] = [
                'id'=>$item->id,
                'name'=>$item->supplier_company_name,
                'supplier_category_id' =>$item->supplier_type_id,
                'contact' =>$item->contract_person,
                'phone' =>$item->phone,
                'email' =>$item->email,
                'address' =>$item->address,
                'status' =>$item->status,
                'remark' =>$item->remark,
                'created_user_id'=>$item->user_id,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
        }
        //dd($data);
        DB::table('suppliers')->insert($data);
    }
    public function migrateCategory(){
        $oldData = DB::table('dk_products_ingredients')->get();
        $oldDataArray = [];
        foreach($oldData as $item) {
            $cn_arr = preg_split('/\s+/', $item->ingredients_cn_name);
            $ko_arr = preg_split('/\s+/', $item->ingredients_kor_name);
            $en_arr = preg_split('/\s+/', $item->ingredients_en_name);
            foreach ($cn_arr as $key => $value) {
                if( $key % 2 == 0 ){
                    $new_arr = [
                        'name' => $cn_arr[$key],
                        'name_en' => $en_arr[$key],
                        'name_ko' => $ko_arr[$key],
                        'sort' => 0,
                        'status' => 1,
                    ];
                    if(!in_array($new_arr, $oldDataArray) && !array_search($cn_arr[$key], array_column($oldDataArray, 'name')) ){
                        array_push($oldDataArray, $new_arr);
                    }
                }
            }
        }
        $data = [];
        foreach ($oldDataArray as $key => $value) {
            $data[] = [
                'name' => $value['name'],
                'name_en' => $value['name_en'],
                'name_ko' => $value['name_kr'],
                'sort' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('goods_components')->insert($data);
    }
    public function migerateCate(){
        $oldData = DB::table('dk_products_type')->where('status', 1)->get();
        $data = [];
        foreach ($oldData as $value) {
            $data[]=[
                'old_id'=>$value->id,
                'name'=>$value->products_type_name,
                'parent_id'=>$value->products_category_id,
                'level' => 2,
                'status'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
        }
        //dd($data);
        DB::table('goods_categories')->insert($data);
    }
    public function migrateSeason()
    {
        $oldData = DB::table('dk_products_season')->get();
        $data = [];
        foreach ($oldData as $item) {
            $year = explode('-', $item->year);
            $data[] = [
                'id' => $item->id,
                'name' => $item->products_season_name,
                'year' => $year[0],
                'season' => $item->season,
                'status' => $item->status,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('goods_seasons')->insert($data);
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
