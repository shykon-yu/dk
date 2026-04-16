<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goods_components', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('中文名称');
            $table->string('name_kr')->comment('韩文名称');
            $table->string('name_en')->comment('英文名称');

            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods_components');
    }
};
