<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up(): void
    {
        Schema::create('craft_methods', function (Blueprint $table) {
            // 主键 ID
            $table->id();

            // 工艺名称
            $table->string('name')->comment('工艺名称');

            // 状态字段（常用 1=启用 0=禁用）
            $table->tinyInteger('status')->default(1)->comment('状态 1=启用 0=禁用');

            // 软删除字段
            $table->softDeletes()->comment('软删除时间');

            // 创建/更新时间
            $table->timestamps();
        });
    }

    /**
     * 回滚迁移
     */
    public function down(): void
    {
        Schema::dropIfExists('craft_methods');
    }
};
