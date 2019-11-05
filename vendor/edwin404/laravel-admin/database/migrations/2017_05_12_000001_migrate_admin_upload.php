<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateAdminUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $datas = \Edwin404\Base\Support\ModelHelper::find('data');
        foreach ($datas as $data) {
            \Edwin404\Base\Support\ModelHelper::add('admin_upload', [
                'category' => $data['category'],
                'dataId' => $data['id'],
                'adminUploadCategoryId' => 0,
            ]);
        }
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
