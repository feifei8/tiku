<?php

namespace Edwin404\Tecmz\Types;


use Edwin404\Base\Support\BaseType;

class SmsSender implements BaseType
{
    const TECMZ = 'tecmz';

    public static function getList()
    {
        return [
            self::TECMZ => '墨子云信',
        ];
    }

}