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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->comment('部门ID');
            $table->string('name');
            $table->tinyInteger('status')->default(1)->comment('启用状态：0禁用 1启用');
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['department_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse');
    }
};
