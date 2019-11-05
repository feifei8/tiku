<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionAnalysis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_analysis', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('questionId')->nullable()->comment('题目ID');
            $table->string('analysis', 2000)->nullable()->comment('题目解析');

            $table->unique(['questionId']);

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
