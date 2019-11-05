<?php

namespace App\Types;


use Edwin404\Base\Support\BaseType;

class PaperExamStatus implements BaseType
{
    const DOING = 1;
    const SUBMITTED = 2;

    public static function getList()
    {
        return [
            self::DOING => '答题中',
            self::SUBMITTED => '答题完成',
        ];
    }


}