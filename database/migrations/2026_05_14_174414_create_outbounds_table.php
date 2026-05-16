<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outbounds', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('department_id')->index()->comment('部门ID');
            $table->unsignedInteger('customer_id')->index()->comment('客户ID');
            $table->unsignedInteger('supplier_id')->index()->comment('供应商ID');

            $table->unsignedInteger('clearance_id')->index()->comment('清关ID');
            $table->unsignedInteger('payment_id')->index()->comment('付款方式ID');

            $table->string('tape')->nullable()->comment('胶带颜色'); // 胶带颜色
            $table->string('outbound_code')->unique()->comment('出库单号');

            $table->tinyInteger('status')->default(0);

            $table->unsignedInteger('created_user_id')->default(0)->comment('创建人ID');
            $table->unsignedInteger('updated_user_id')->default(0)->comment('修改人ID');

            $table->date('outbound_at')->comment('出库日期');

            $table->softDeletes(); // 软删除
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outbounds');
    }
};
