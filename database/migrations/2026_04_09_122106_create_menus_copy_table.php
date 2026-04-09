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
        Schema::create('menus_copy', function (Blueprint $table) {
            $table->id();
            $table->string('column');
            $table->tinyInteger('status')->default(1);
            $table->string('url');
            $table->unsignedBigInteger('pid');
            $table->string('user_group');
            $table->tinyInteger('column_type')->default(0);
            $table->string('update_time');
            $table->unsignedBigInteger('index_');
            $table->unsignedBigInteger('p_index');
            $table->tinyInteger('type')->default(0);
            $table->timestamps();
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('index_');
            $table->unsignedBigInteger('p_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus_copy');
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('index_');
            $table->dropColumn('p_index');
        });
    }
};
