<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_answer', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('questionId')->nullable()->comment('别名');
            $table->string('answer', 2000)->nullable()->comment('题目答案');

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
