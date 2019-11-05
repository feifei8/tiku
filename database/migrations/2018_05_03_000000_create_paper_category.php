<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_category', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('pid')->comment('父ID')->nullable();
            $table->integer('sort')->comment('排序，越小越靠前')->nullable();

            $table->string('name', 20)->nullable()->comment('名称');

        });


        Schema::table('paper', function (Blueprint $table) {

            $table->integer('categoryId')->comment('分类ID')->nullable();

            $table->index(['categoryId']);

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
