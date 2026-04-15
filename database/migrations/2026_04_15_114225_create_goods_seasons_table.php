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
        Schema::create('goods_seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('year');
            $table->unsignedTinyInteger('season');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1=启用 0=禁用');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['year', 'season']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_seasons');
    }
};
