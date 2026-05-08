<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inbound_items', function (Blueprint $table) {
            $table->id();

            $table->integer('inbound_id')->default(0)->index()->comment('入库总单ID');
            $table->integer('order_id')->default(0)->index()->comment('订单ID');
            $table->integer('goods_id')->default(0)->index()->comment('商品ID');
            $table->integer('sku_id')->default(0)->comment('SKU ID');

            $table->integer('quantity')->default(0)->comment('入库数量');
            $table->decimal('price', 10, 2)->default(0)->comment('单价');
            $table->decimal('amount', 10, 2)->default(0)->comment('金额');

            $table->tinyInteger('status')->default(0)->index()->comment('状态');
            $table->text('remark')->nullable()->comment('备注');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inbound_items');
    }
};
