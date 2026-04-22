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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('货币名称：美元、人民币');
            $table->string('code')->unique()->comment('货币代码：USD,CNY');
            $table->string('symbol')->comment('货币符号：$ ￥ €');

            $table->decimal('rate', 16, 6)->default(1)->comment('汇率');
            $table->tinyInteger('is_base')->default(0)->comment('是否本位币');

            $table->tinyInteger('status')->default(1)->comment('状态：1启用');
            $table->integer('sort')->default(0)->comment('排序');

            $table->string('decimal_sep')->default('.')->comment('小数点符号');
            $table->string('thousand_sep')->default(',')->comment('千位分隔符');
            $table->tinyInteger('decimal_digits')->default(2)->comment('小数位数');

            $table->softDeletes(); // 软删除
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
        Schema::dropIfExists('currencies');
    }
};
