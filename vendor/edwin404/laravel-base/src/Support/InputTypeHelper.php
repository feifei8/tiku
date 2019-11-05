<?php

namespace Edwin404\Base\Support;


class InputTypeHelper
{
    public static function isEmail($email)
    {
        return preg_match('/^[a-zA-Z0-9_\\-\\.]+@[a-zA-Z0-9_\\-]+[\\.a-zA-Z0-9_\\-]+$/ ', $email);
    }

    public static function isPhone($phone)
    {
        return preg_match('/^1[0-9]{10}$/', $phone);
    }

    public static function isDomain($domain)
    {
        return preg_match('/([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,10}/i', $domain);
    }

    public static function isMoney($money)
    {
        if ($money < 0.01) {
            return false;
        }
        if ($money > 10000 * 100) {
            return false;
        }
        return true;
    }
}