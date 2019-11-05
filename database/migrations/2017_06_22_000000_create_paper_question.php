<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_question', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('paperId')->nullable()->comment('试卷ID');
            $table->integer('questionId')->nullable()->comment('题目ID');

            $table->string('score', 400)->nullable()->comment('分数');

            $table->unique(['paperId', 'questionId']);

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
