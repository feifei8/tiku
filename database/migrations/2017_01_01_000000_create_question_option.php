<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_option', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('questionId')->nullable()->comment('别名');
            $table->tinyInteger('isAnswer')->nullable()->comment('是否是答案');

            $table->string('option', 2000)->nullable()->comment('题目选项');

            $table->index(['questionId']);

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
