<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goods_sku_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id');
            $table->unsignedBigInteger('sku_id');
            $table->unsignedBigInteger('warehouse_id');

            $table->integer('stock')->default(0);       // 总库存
            $table->integer('lock_stock')->default(0);  // 锁定库存
            $table->integer('available_stock')->default(0); // 可用库存

            // 唯一索引：一个SKU在一个仓库只能有一条记录
            $table->unique(['sku_id', 'warehouse_id']);

            $table->index('goods_id');
            $table->index('sku_id');
            $table->index('warehouse_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods_sku_stocks');
    }
};
