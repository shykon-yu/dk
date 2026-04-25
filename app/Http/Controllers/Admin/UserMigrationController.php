<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Goods;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Sku;

class UserMigrationController extends Controller
{
    protected $chunkSize = 200;// 每次插200条，避免数据包太大
    //将老users数据表修改成新表样式集合，存入users表里
    public function migrate()
    {
        $this->migrateComponent2();
    }

    public function migrateComponent2()
    {
        $goods = Goods::query()->whereHas('components', function ($query) {
           $query->where('goods_component_id',12);
        })
            ->with('components')
            ->get()->toArray();
        //dd($goods);
        $data = [];
        foreach( $goods as $good ){
            $data[$good['id']] = [];
            foreach( $good['components'] as $component ){
                if( $component['id'] == 12 ){
                    $component_id = 14;
                }else{
                    $component_id = $component['id'];
                }
                $data[$good['id']][$component_id] = ['percent' => $component['pivot']['percent']];
            }
        }
//        dd($data);
        foreach( $data as $goodsId => $components ){
            $goods = Goods::query()->find($goodsId);
            $goods->components()->sync($components);
        }
    }

    public function migratePer(){
        $oldData = DB::table('permissions')->get();
        $data = [];
        foreach ($oldData as $value) {
            if (str_contains($value->name, 'index')) {
                $array = explode('.', $value->name);
                if( isset($data[$value->id]) ){
                    $mergeData = [
                        'name' => $value->name,
                    ];
                }else{
                    $data[$value->id] = [

                    ];
                }
            }
        }
        dd($oldData);
    }
    public function migrateStock(){
        $oldData = DB::table('dk_inventory')->get();
        $data = [];
        foreach ($oldData as $item){
            $color = DB::table('skus')->where('id',$item->products_color_id)->get();
            if($color->isEmpty()){
               continue;
            }
            $data[]=[
                'goods_id'=>$item->products_id,
                'warehouse_id'=>$item->warehouse_id,
                'sku_id'=>$item->products_color_id,
                'stock'=>$item->number,
                'lock_stock'=>0,
                'available_stock'=>$item->number,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
        }
        DB::table('goods_sku_stocks')->insert($data);
    }
    public function migrateWarehouse(){
        $oldData = DB::table('dk_warehouse')->get();
        $data = [];
        foreach ($oldData as $item){
            $data[] = [
                'id' => $item->id,
                'department_id' => $item->department_id,
                'name' => $item->warehouse_name,
                'status'=>1,
                'sort'=>0,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
        }
        DB::table('warehouses')->insert($data);
    }
    public function migrateComponent(){
        $oldData = DB::table('goods')->get();
        $componentArray = [];
        foreach ($oldData as $item){
            $component = DB::table('dk_products_ingredients')->where('id',$item->old_component_id)->first();
            if(!$component){
                continue;
            }
            $cn_arr = preg_split('/\s+/', $component->ingredients_cn_name);
            $total = count($cn_arr);
            for($i = 0; $i < $total; $i=$i+2){
                $component_data = DB::table('goods_components')->where('name',$cn_arr[$i])->first();
                if(!$component_data){
                    continue;
                }
                $componentArray[] = [
                    'goods_id'=>$item->id,
                    'goods_component_id'=>$component_data->id,
                    'percent' => substr($cn_arr[$i+1],0,-1),
                ];
            }
        }
        DB::table('goods_goods_component')->insert($componentArray);
    }
    public function migrateimage(){
        $oldData = DB::table('dk_products_color')->whereNotNull('item_no')->get();
        $data = [];
        foreach ($oldData as $item){
            $isset = DB::table('goods')->where('id',$item->products_id)->first();
            if( is_null($isset) ){
                continue;
            }
            $data[] = [
                'id' => $item->id,
                'size' => 'os',
                'goods_id'=>$item->products_id,
                'color' => $item->color_name,
                'status' => $item->status,
                'created_at' => now(),
                'updated_at' => now(),
                'stock' => 0,
                'sell_price' => $item->sell_price,
                'sell_price2' => $item->sell_price2,
                'cost_price' => $item->purchase_price,
                'cost_price2' => $item->purchase_price2,
                'process_price' => $item->process_price,
                'process_step2_price' => $item->process_price2,
            ];
        }
        foreach ($data as $item) {
            Sku::create($item);
        }
    }
    public function migrateGoods()
    {
        $data = [];
        $oldData = DB::table('dk_products')->get();
        foreach ($oldData as $item) {
            $data[] = [
                'id' => $item->id,
                'department_id' => $item->department_id,
                'customer_id' => $item->custom_id,
                'supplier_id' => $item->supplier_company_id,
                'name' => $item->products_name,
                'customer_sku' => $item->item_no,
                'brand_logo' => $item->brand_logo,
                'category_id' => $item->products_category_id,
                'season_id' => $item->products_season_id,
                'status' => $item->status,
                'is_star' => $item->star_marks,
                'main_image' => $item->products_image_id,
                'remark' => $item->remark,
                'created_at' => $item->add_time_date,
                'updated_at' => $item->update_time_date,
                'created_user_id' => $item->user_id,
            ];
        }
        foreach(array_chunk($data, 30) as $chunk) {
            foreach ($chunk as $item) {
                Goods::create($item);
            }
        }
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
