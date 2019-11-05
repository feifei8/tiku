<?php

namespace Edwin404\Base\Support;


use Overtrue\Pinyin\Pinyin;

class ChineseHelper
{
    private static $instance = null;

    private static function ins()
    {
        if (null === self::$instance) {
            self::$instance = new Pinyin();
        }
        return self::$instance;
    }

    public static function firstLetter($str)
    {
        if (empty($str)) {
            return null;
        }
        $cs = self::ins()->convert($str);
        if (empty($cs[0])) {
            return null;
        }
        return $cs[0]{0};
    }
}