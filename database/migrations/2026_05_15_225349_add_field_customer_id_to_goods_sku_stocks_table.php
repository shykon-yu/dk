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
        Schema::table('goods_sku_stocks', function (Blueprint $table) {
            // 添加客户ID、部门ID字段
            $table->unsignedInteger('customer_id')->index()->comment('客户ID');
            $table->unsignedInteger('department_id')->index()->comment('部门ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_sku_stocks', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'department_id']);
        });
    }
};
