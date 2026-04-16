<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // 所属部门
            $table->unsignedBigInteger('department_id')->comment('部门ID');

            // 客户名称（多语言）
            $table->string('name')->comment('客户名称');
            $table->string('name_kr')->nullable()->comment('韩文名称');

            // 品牌与货号
            $table->string('brand_logo')->nullable()->comment('品牌LOGO');
            $table->string('sku_prefix')->nullable()->comment('货号前缀');

            // 业务关联
            $table->unsignedBigInteger('clearance_id')->comment('清关方式ID');
            $table->unsignedBigInteger('payment_id')->comment('支付方式ID');

            // 联系方式
            $table->string('contact')->nullable()->comment('联系人');
            $table->string('phone')->nullable()->comment('电话');
            $table->string('email')->nullable()->comment('邮箱');
            $table->text('address')->nullable()->comment('地址');

            // 子公司结构
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父客户ID 0=顶级');

            // 操作人
            $table->unsignedBigInteger('created_user_id')->comment('创建人ID');
            $table->unsignedBigInteger('updated_user_id')->nullable()->comment('修改人ID');

            // 软删除
            $table->softDeletes();
            $table->timestamps();

            // 索引（加速查询）
            $table->index('department_id');
            $table->index('parent_id');
            $table->index('clearance_id');
            $table->index('payment_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
