<?php

namespace Edwin404\Base\Support;

class TypeHelper
{
    public static function name($typeCls, $value)
    {
        $list = $typeCls::getList();
        foreach ($list as $k => $v) {
            if ($k == $value) {
                return $v;
            }
        }
        return null;
    }
}