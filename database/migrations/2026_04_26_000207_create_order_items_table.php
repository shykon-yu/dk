<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->index()->comment('主订单ID');
            $table->bigInteger('goods_id')->index()->comment('商品ID');
            $table->bigInteger('sku_id')->index()->comment('SKU ID');

            $table->string('color_card')->nullable()->comment('色卡');

            $table->integer('number')->default(0)->comment('订货数量');
            $table->integer('received_quantity')->default(0)->comment('已收货数量');

            $table->integer('unit_id')->default(0)->comment('单位ID');
            $table->integer('currency_id')->default(0)->comment('货币ID');
            $table->decimal('price', 10, 2)->default(0)->comment('单价');

            $table->tinyInteger('status')->default(0)->index()->comment('状态:0刚下单 1加工中 2部分入库 3全部入库');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
