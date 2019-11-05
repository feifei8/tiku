<?php

namespace Edwin404\Admin\Type;


use Edwin404\Base\Support\BaseType;

class AdminLogType implements BaseType
{
    const INFO = 1;
    const ERROR = 2;

    public static function getList()
    {
        return [
            self::INFO => '信息',
            self::ERROR => '错误',
        ];
    }
}