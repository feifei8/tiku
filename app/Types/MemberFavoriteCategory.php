<?php

namespace App\Types;


use Edwin404\Base\Support\BaseType;

class MemberFavoriteCategory implements BaseType
{
    const QUESTION = 'question';

    public static function getList()
    {
        return [
            self::QUESTION => '题目',
        ];
    }

}