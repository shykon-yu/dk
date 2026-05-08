<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();

            $table->integer('department_id')->default(0)->index()->comment('部门ID');
            $table->integer('customer_id')->default(0)->index()->comment('客户ID');
            $table->integer('supplier_id')->default(0)->index()->comment('供应商ID');
            $table->integer('warehouse_id')->default(0)->index()->comment('仓库ID');

            $table->string('inbound_code', 50)->unique()->comment('入库单号');

            $table->tinyInteger('status')->default(0)->index()->comment('状态 0=待入库 1=已完成');
            $table->string('batch_no', 50)->default('')->comment('批次号');

            $table->integer('created_user_id')->default(0)->comment('创建人');
            $table->integer('updated_user_id')->default(0)->comment('修改人');

            $table->timestamp('inbound_at')->index()->comment('入库时间');

            $table->timestamps();
            $table->softDeletes(); // 软删除
        });
    }

    public function down()
    {
        Schema::dropIfExists('inbounds');
    }
};
