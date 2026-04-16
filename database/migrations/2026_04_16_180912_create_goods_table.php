<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('department_id')->comment('部门ID');
            $table->unsignedBigInteger('customer_id')->comment('客户ID');
            $table->unsignedBigInteger('supplier_id')->comment('默认供应商ID');

            $table->string('name')->comment('产品名称');
            $table->string('code')->unique()->comment('内部唯一编码');
            $table->string('customer_sku')->comment('客户定制货号');
            $table->string('brand_logo')->nullable()->comment('品牌LOGO');
            $table->unsignedBigInteger('category_id')->comment('分类ID');
            $table->unsignedBigInteger('season_id')->comment('季节ID');

            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->tinyInteger('is_star')->default(0)->comment('星标产品 1是 0否');

            $table->string('main_image')->nullable()->comment('主图');
            $table->text('remark')->nullable()->comment('备注');

            $table->unsignedBigInteger('created_user_id');
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // 软删除
            $table->softDeletes();
            $table->timestamps();

            // 索引（加速查询，外贸ERP必备）
            $table->index('department_id');
            $table->index('customer_id');
            $table->index('supplier_id');
            $table->index('category_id');
            $table->index('season_id');
            $table->index('status');
            $table->index('is_star');
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods');
    }
};
