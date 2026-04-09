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
        Schema::create('users_copy', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->comment('老账号');
            $table->string('password')->comment('老密码');
            $table->string('name')->nullable()->comment('老姓名');
            $table->string('email')->nullable()->comment('老邮箱');
            $table->text('departments')->nullable()->comment('老部门');
            $table->string('phone_number')->nullable()->comment('手机号');
            $table->integer('section_id')->nullable();
            $table->integer('group_id')->nullable(); // 老的分组ID
            $table->tinyInteger('status')->default(1);
            $table->string('open_id')->nullable();

            // 老项目时间相关（迁移时忽略）
            $table->string('add_time')->nullable();
            $table->string('update_time')->nullable();
            $table->string('delete_time')->nullable();

            $table->string('add_time_date')->nullable();
            $table->string('update_time_date')->nullable();
            $table->string('delete_time_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_copy');
    }
};
