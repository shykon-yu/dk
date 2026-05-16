<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outbound_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outbound_id')->index()->comment('出库单ID');

            // 基础关联
            $table->string('brand_logo')->nullable()->comment('品牌logo');
            $table->unsignedInteger('warehouse_id')->index()->comment('仓库ID');
            $table->unsignedInteger('goods_id')->index()->comment('产品ID');
            $table->unsignedInteger('sku_id')->index()->comment('规格SKU ID');

            // 唛头 & 箱号区间
            $table->string('shipping_mark')->nullable()->comment('唛头');
            $table->integer('carton_no_start')->default(0)->comment('起始箱号');
            $table->integer('carton_no_end')->default(0)->comment('结束箱号');

            // 数量
            $table->integer('carton_qty')->default(0)->comment('总箱数');
            $table->integer('unit_carton_qty')->default(0)->comment('单箱数量');
            $table->integer('quantity')->default(0)->comment('货物数量');

            // 货币单价
            $table->unsignedInteger('currency_id')->default(0)->comment('货币ID');
            $table->decimal('price',10,2)->default(0)->comment('单价');

            // 外箱尺寸 & 体积
            $table->decimal('carton_length',10,2)->default(0)->comment('箱长cm');
            $table->decimal('carton_width',10,2)->default(0)->comment('箱宽cm');
            $table->decimal('carton_height',10,2)->default(0)->comment('箱高cm');
            $table->decimal('cbm',12,2)->default(0)->comment('总体积CBM');

            // 做工方式 默认1机织
            $table->unsignedInteger('craft_method_id')->default(1)->comment('做工方式 1机织');

            // 重量
            $table->decimal('gross_weight',10,2)->default(0)->comment('毛重KG');
            $table->decimal('net_weight',10,2)->default(0)->comment('净重KG');

            // 状态 0正常 1已盘点
            $table->tinyInteger('status')->default(0)->comment('0正常 1已盘点');

            $table->text('remark')->nullable()->comment('备注');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outbound_items');
    }
};
