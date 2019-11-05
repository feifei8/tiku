<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_tag', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('groupId')->nullable()->comment('标签组ID');
            $table->string('title', 100)->nullable()->comment('名称');
            $table->string('cover', 200)->nullable()->comment('图片');
            $table->string('description', 2000)->nullable()->comment('介绍');

            $table->index(['groupId']);

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
