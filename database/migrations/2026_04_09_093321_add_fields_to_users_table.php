<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 你老项目需要加的字段（全部在这里加）
            $table->string('username')->unique()->after('id'); // 登录账号
            $table->text('department_id')->nullable()->after('password'); // 部门
            $table->string('phone_number')->nullable()->after('department_id'); // 手机号
            $table->tinyInteger('status')->default(1)->after('phone_number'); // 状态
            $table->integer('section_id')->nullable()->after('status'); // 部门ID
            $table->integer('role_id')->default(0)->after('section_id'); // 角色ID ✅ 改这里
            $table->string('open_id')->nullable()->after('email'); // 第三方登录

            // 软删除 deleted_at（你要的）
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'department_id',
                'phone_number',
                'status',
                'section_id',
                'role_id',  // 对应回滚
                'open_id',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
