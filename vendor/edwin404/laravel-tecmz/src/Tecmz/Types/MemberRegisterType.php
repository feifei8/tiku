<?php

namespace Edwin404\Tecmz\Types;


use Edwin404\Base\Support\BaseType;

class MemberRegisterType implements BaseType
{
    const USERNAME = 1;
    const PHONE = 2;
    const EMAIL = 3;

    public static function getList()
    {
        return [
            self::USERNAME => '用户名注册',
            self::PHONE => '手机注册',
            self::EMAIL => '邮箱注册',
        ];
    }

}