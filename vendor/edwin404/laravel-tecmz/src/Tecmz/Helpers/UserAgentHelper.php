<?php

namespace Edwin404\Tecmz\Helpers;

use Edwin404\Api\Type\Platform;
use Illuminate\Support\Facades\Request;

class UserAgentHelper
{
    const SOURCE_OTHER = 0;
    const SOURCE_APP = 1;

    private static $_info = [
        'type' => null,
        'version' => null,
        'channel' => null,
        /** @see Platform */
        'os' => null,
        'osVersion' => null,
        'device' => null,

    ];

    public static function source($type = null, $channel = null)
    {
        $tecmzClient = Request::header('Tecmz-Client');
        if (empty($tecmzClient)) {
            $tecmzClient = Request::header('User-Agent');
        }
        //$userAgent = 'com.tecmz.notesns/1.0.0 heezhi (android 6.0.1; Redmi 3S) Mozilla/5.0 (Linux; Android 6.0.1; Redmi 3S Build/MMB29M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/51.0.2704.81 Mobile Safari/537.36 Html5Plus/1.0';
        if (null === $type) {
            $type = '[a-z]+';
        }
        if (null === $channel) {
            $channel = '[a-z]+';
        }
        // com.tecmz.notesns/1.0.0 渠道(notesns,heezhi) (android|ios 1.0.0; Redmi S 2) + Old
        if (preg_match('/^com\\.tecmz\\.(' . $type . ')\\/(\\d+\\.\\d+\\.\\d+) (' . $channel . ') \\((ios|android) (.*?); (.*?)\\)/', $tecmzClient, $mat)) {
            self::$_info['type'] = $mat[1];
            self::$_info['version'] = $mat[2];
            self::$_info['channel'] = $mat[3];
            self::$_info['os'] = $mat[4];
            self::$_info['osVersion'] = $mat[5];
            self::$_info['device'] = $mat[6];

            switch (self::$_info['os']) {
                case 'ios':
                    self::$_info['os'] = Platform::IOS;
                    break;
                case 'android':
                    self::$_info['os'] = Platform::ANDROID;
                    break;
            }
            return self::SOURCE_APP;
        }
        return self::SOURCE_OTHER;
    }
}