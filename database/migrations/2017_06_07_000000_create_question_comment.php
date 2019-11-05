<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_comment', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('questionId')->nullable()->comment('题目ID');
            $table->integer('memberUserId')->nullable()->comment('用户ID');

            $table->text('content')->nullable()->comment('内容');

            $table->index(['questionId']);
            $table->index(['memberUserId']);

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
