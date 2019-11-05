<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTagGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_tag_group', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('title', 100)->nullable()->comment('名称');
            $table->integer('pid')->nullable()->comment('上级分类');
            $table->integer('sort')->nullable()->comment('排序');

            $table->index(['pid']);

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
