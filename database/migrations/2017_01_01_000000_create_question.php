<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('alias', 16)->nullable()->comment('别名');
            $table->string('question', 2000)->nullable()->comment('题干');

            /** @see \App\Types\QuestionType */
            $table->integer('type')->nullable()->comment('类型');
            $table->integer('parentId')->nullable()->comment('父题干ID');

            $table->string('tag', 500)->nullable()->comment('题目标签,如 :1::2:');

            $table->integer('clickCount')->nullable()->comment('点击量');
            $table->integer('testCount')->nullable()->comment('测试量');
            $table->integer('passCount')->nullable()->comment('通过量');
            /**
             * 表示题目项个数(具体得分项)
             */
            $table->integer('itemCount')->nullable()->comment('题目数量');
            $table->integer('commentCount')->nullable()->comment('评论数');

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
