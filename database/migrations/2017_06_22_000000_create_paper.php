<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('alias', 16)->nullable()->comment('别名');
            $table->string('title', 100)->nullable()->comment('试卷名称');

            $table->tinyInteger('isPublic')->nullable()->comment('是否未登录可访问');

            $table->integer('passScore')->nullable()->comment('及格线');
            $table->integer('totalScore')->nullable()->comment('总分');

            $table->integer('questionCount')->nullable()->comment('题目总数');

            $table->tinyInteger('timeLimitEnable')->nullable()->comment('时间限制开启');
            $table->integer('timeLimitValue')->nullable()->comment('时间限制分钟数');

            $table->unique(['alias']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
