<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperExamQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_exam_question', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('examId')->nullable()->comment('考试ID');
            $table->integer('questionId')->nullable()->comment('题目ID');

            $table->string('answer', 2000)->nullable()->comment('题目答案');

            $table->tinyInteger('isJudge')->nullable()->comment('是否已经计算分数');
            $table->string('score', 1000)->nullable()->comment('分数');

            $table->unique(['examId', 'questionId']);

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
