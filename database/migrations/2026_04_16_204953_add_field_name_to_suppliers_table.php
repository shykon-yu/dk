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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('name')->comment('供应商名称');

            // 联系方式
            $table->string('contact')->nullable()->comment('联系人');
            $table->string('phone')->nullable()->comment('电话');
            $table->string('email')->nullable()->comment('邮箱');
            $table->text('address')->nullable()->comment('地址');

            // 状态与备注
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->text('remark')->nullable()->comment('备注');

            // 创建人/修改人
            $table->unsignedBigInteger('created_user_id')->comment('创建人ID');
            $table->unsignedBigInteger('updated_user_id')->nullable()->comment('修改人ID');

            // 软删除
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
        Schema::table('suppliers', function (Blueprint $table) {
            Schema::dropColumns('name');
            Schema::dropColumns('contact');
            Schema::dropColumns('phone');
            Schema::dropColumns('email');
            Schema::dropColumns('address');
            Schema::dropColumns('status');
            Schema::dropColumns('remark');
            Schema::dropColumns('created_user_id');
            Schema::dropColumns('updated_user_id');
            $table->softDeletes();

        });
    }
};
