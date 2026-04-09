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
        // 执行迁移时：删除表
        Schema::dropIfExists('users_copy');
    }

    public function down()
    {
        // 回滚时：重新创建表（你不需要可以留空，但建议写上）
//        Schema::create('users_copy', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//        });
    }
};
