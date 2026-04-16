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
        Schema::create('clearances', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('清关方式名称');
            $table->string('name_kr')->nullable()->comment('韩文名称');
            $table->integer('sort')->default(0)->comment('排序（数字越小越靠前）'); // 新增
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用'); // 新增
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('支付方式名称');
            $table->string('name_kr')->nullable()->comment('韩文名称');
            $table->integer('sort')->default(0)->comment('排序'); // 新增
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用'); // 新增
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clearances');
        Schema::dropIfExists('payments');
    }
};
