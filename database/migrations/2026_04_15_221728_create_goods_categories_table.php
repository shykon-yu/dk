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
        Schema::create('goods_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // 类目名称
            $table->integer('parent_id')->default(0); // 父级ID  🔥核心
            $table->integer('level')->default(1);     // 层级 1=一级 2=二级 3=三级
            $table->integer('sort')->default(0);      // 排序
            $table->tinyInteger('status')->default(1); // 状态
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
        Schema::dropIfExists('goods_categories');
    }
};
