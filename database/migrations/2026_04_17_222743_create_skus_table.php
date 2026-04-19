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
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->index()->comment('商品ID');
            $table->string('color')->comment('颜色（自定义）');
            $table->string('size')->comment('尺寸（自定义）');
            $table->integer('stock')->default(0)->comment('当前库存（老数据直接录入这里）');
            $table->decimal('sell_price', 10,2)->default(0)->comment('销售价1');
            $table->decimal('sell_price2', 10,2)->default(0)->comment('销售价2');
            $table->decimal('cost_price', 10,2)->default(0)->comment('成本价1');
            $table->decimal('cost_price2', 10,2)->default(0)->comment('成本价2');
            $table->decimal('process_price', 10,2)->default(0)->comment('加工价');
            $table->decimal('process_step2_price', 10,2)->default(0)->comment('进一步加工价');
            $table->decimal('cost_all_price', 10,2)->default(0)->comment('总成本1 = cost + process + process2');
            $table->decimal('cost_all_price2', 10,2)->default(0)->comment('总成本2 = cost2 + process + process2');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['goods_id','color','size']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skus');
    }
};
