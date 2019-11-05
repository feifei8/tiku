<?php

namespace EdwinFound\Utils;


class FormatUtil
{
    public static function telephone($number)
    {
        $number = str_replace([
            '+86',
            '+',
            ' ',
            '(',
            ')',
            '-',
            '（',
            '）',
            '',
            ' ',
            '　',
            '"',
            ';',
            "\t",
        ], '', $number);
        $number = trim($number);
        if (!preg_match('/^[0-9]{3,20}$/', $number)) {
            return null;
        }
        return $number;
    }
}