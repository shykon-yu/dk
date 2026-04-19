<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_colors', function (Blueprint $table) {
            $table->id(); // 主键ID
            $table->string('name')->unique()->comment('中文颜色名（唯一，避免重复）');
            $table->string('name_en')->nullable()->comment('英文颜色名（适配外贸）');
            $table->string('name_kr')->nullable()->comment('韩文颜色名（对接韩国客户）');
            $table->string('code', 20)->nullable()->comment('颜色码/色号（十六进制#000000或厂家色号）');
            $table->integer('sort')->default(0)->comment('排序（数字越大越靠前）');
            $table->tinyInteger('status')->default(1)->comment('状态 1-启用 0-禁用');
            $table->timestamps(); // created_at / updated_at
            $table->softDeletes();

            // 索引优化（可选，高频查询时提升速度）
            $table->index('sort');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_colors');
    }
};
