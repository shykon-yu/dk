<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('department_id')->index()->comment('所属部门ID');
            $table->integer('customer_id')->index()->comment('客户ID');
            $table->integer('supplier_id')->index()->comment('工厂/供应商ID');
            $table->string('order_code')->unique()->comment('订单编号');

            $table->tinyInteger('status')->default(1)->index()->comment('订单状态：0刚下单，1加工中，2已入库，3全部入库已完成');

            $table->timestamp('ordered_at')->nullable()->comment('下单时间');
            $table->timestamp('delivery_at')->nullable()->comment('期望交货时间');

            $table->text('remark')->nullable()->comment('订单备注');
            $table->tinyInteger('is_star')->default(0)->comment('星标订单 0否 1是');
            $table->text('status_remark')->nullable()->comment('状态备注');
            $table->integer('created_user_id')->default(0)->comment('创建人ID');
            $table->integer('updated_user_id')->default(0)->comment('修改人ID');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
