<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_excel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->default(0)->index()->comment('关联订单ID');
            $table->string('name')->comment('文件原始名称');
            $table->string('file_path')->comment('文件访问路径');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_excel');
    }
};
