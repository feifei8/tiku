<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Facades\Request;

class RequestHelper
{
    public static function currentPageUrl()
    {
        if (Request::ajax()) {
            $redirect = Request::server('HTTP_REFERER');
        } else {
            $redirect = Request::fullUrl();
        }
        return $redirect;
    }

    public static function currentPageUrlWithOutQueries()
    {
        return Request::url();
    }

    public static function mergeQueries($pair = [])
    {
        $gets = (!empty($_GET) && is_array($_GET)) ? $_GET : [];
        foreach ($pair as $k => $v) {
            $gets[$k] = $v;
        }

        $urls = [];
        foreach ($gets as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (is_array($v)) {
                $v = $v[0];
            } else {
                $v = urlencode($v);
            }
            $urls[] = "$k=" . $v;
        }

        return join('&', $urls);
    }

    public static function domain()
    {
        return Request::server('HTTP_HOST');
    }

    public static function schema()
    {
        static $schema = null;
        if (null === $schema) {
            if (Request::secure()) {
                $schema = 'https';
            } else {
                $schema = 'http';
            }
        }
        return $schema;
    }

    public static function domainUrl()
    {
        return self::schema() . '://' . self::domain();
    }

    public static function isPost()
    {
        return Request::isMethod('post');
    }

}