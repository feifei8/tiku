<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Base\Support\CurlHelper;
use Edwin404\Base\Support\SignHelper;
use Illuminate\Support\Facades\Cache;

/**
 * Class TecmzApiHelper
 * @package Edwin404\Tecmz\Helpers
 *
 * @deprecated
 * use TecmzApi instead
 */
class TecmzApiHelper
{
    const API = 'http://api.tecmz.com/api';

    public static function shipping($appId, $appSecret, $type, $no)
    {
        $param = [];
        $param['type'] = $type;
        $param['no'] = $no;
        return self::request('express', $appId, $appSecret, $param, 0);
    }

    private static function request($api, $appId, $appSecret, $param, $cacheMinutes = 0)
    {
        $param['app_id'] = $appId;
        if ($cacheMinutes > 0) {
            $flag = $api . '-' . md5(serialize($param) . $appSecret);
        } else {
            $flag = null;
        }
        $param['timestamp'] = time();
        $param['sign'] = SignHelper::common($param, $appSecret);
        if ($flag) {
            return Cache::remember($flag, $cacheMinutes, function () use ($param, $api) {
                return CurlHelper::getStandardJson(self::API . '/' . $api, $param);
            });
        } else {
            return CurlHelper::getStandardJson(self::API . '/' . $api, $param);
        }
    }
}