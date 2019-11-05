<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_user', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('username', 50)->nullable()->comment('用户名');
            $table->string('phone', 20)->nullable()->comment('手机');
            $table->string('email', 200)->nullable()->comment('邮箱');
            $table->char('password', 32)->nullable()->comment('密码');
            $table->char('passwordSalt', 16)->nullable()->comment('密码Salt');
            $table->timestamp('lastLoginTime')->nullable()->comment('上次登录时间');
            $table->string('lastLoginIp', 20)->nullable()->comment('上次登录Ip');
            $table->boolean('phoneVerified')->nullable()->comment('手机已验证');
            $table->boolean('emailVerified')->nullable()->comment('邮箱已验证');

            $table->string('avatar', 50)->nullable()->comment('头像(小)');
            $table->string('avatarMedium', 50)->nullable()->comment('头像(中)');
            $table->string('avatarBig', 50)->nullable()->comment('头像(大)');

            //TODO 实际使用过程中按照需要再继续增删字段
            
            /** @see \Edwin404\Member\Types\Gender */
            $table->tinyInteger('gender')->nullable()->comment('性别');
            $table->string('realname', 20)->nullable()->comment('真实姓名');
            $table->string('signature', 200)->nullable()->comment('个性签名');


            $table->index('username');
            $table->index('phone');
            $table->index('email');

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
