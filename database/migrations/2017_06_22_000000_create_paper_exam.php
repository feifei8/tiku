<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperExam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_exam', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('memberUserId')->nullable()->comment('用户ID');
            $table->integer('paperId')->nullable()->comment('试卷ID');

            /** @see \App\Types\PaperExamStatus */
            $table->tinyInteger('status')->nullable()->comment('答题状态');

            $table->timestamp('startTime')->nullable()->comment('开始时间');
            $table->timestamp('endTime')->nullable()->comment('结束时间');
            $table->integer('score')->nullable()->comment('分数');

            $table->tinyInteger('isAutoJudge')->nullable()->comment('是否已经自动计算分数');
            $table->tinyInteger('isJudge')->nullable()->comment('是否已经确认分数');

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
