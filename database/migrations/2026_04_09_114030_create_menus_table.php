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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('route')->nullable()->comment('菜单路由');
            $table->string('url')->nullable()->comment('菜单链接');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父菜单ID，0为顶级');
            $table->integer('order')->default(0)->comment('排序值');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();
            //索引优化
            $table->index(['parent_id', 'status','order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
