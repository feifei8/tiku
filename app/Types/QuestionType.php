<?php

namespace App\Types;

use Edwin404\Base\Support\BaseType;

class QuestionType implements BaseType
{
    const SINGLE_CHOICE = 1;
    const MULTI_CHOICES = 2;
    const TRUE_FALSE = 3;
    const FILL = 4;
    const TEXT = 5;
    const GROUP = 6;

    public static function getList()
    {
        return [
            self::SINGLE_CHOICE => '单选题',
            self::MULTI_CHOICES => '多选题目',
            self::TRUE_FALSE => '判断题',
            self::FILL => '填空题',
            self::TEXT => '问答题',
            self::GROUP => '多题目',
        ];
    }
}