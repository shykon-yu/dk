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
        Schema::create('goods_goods_component', function (Blueprint $table) {
            $table->id();

            // 关联两个表
            $table->unsignedBigInteger('goods_id')->index();
            $table->unsignedBigInteger('goods_component_id')->index();

            // 额外字段：成分百分比 ✅ 你要的就是这个
            $table->decimal('percent', 5, 2)->default(0)->comment('成分百分比');

            // 联合索引（加速查询）
            $table->unique(['goods_id', 'goods_component_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_goods_components');
    }
};
