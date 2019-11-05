<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Facades\Cache;

class LBSHelper
{
    public static function calcChinaProvince($data)
    {
        if (empty($data)) {
            return '未知';
        }
        if (!empty($data['ipProvince'])) {
            return str_replace(['省', '市'], '', $data['ipProvince']);
        }
        if (!empty($data['province'])) {
            return str_replace(['省', '市'], '', $data['province']);
        }
        return '未知';
    }


    public static function locationByIP($ip)
    {
        $cacheKey = 'LBS.Location.ip' . $ip;
        $cached = Cache::get($cacheKey, null);
        if ($cached) {
            return $cached;
        }
        $order = [1, 2, 3];
        shuffle($order);
        $cached = null;
        foreach ($order as $type) {
            $cached = self::locationByIPRandom($type, $ip);
            if ($cached) {
                Cache::put($cacheKey, $cached, 30);
                return $cached;
            }
        }
        return null;
    }

    private static function locationByIPRandom($type, $ip)
    {
        switch ($type) {
            case 1:
                $cityQueryRet = self::getContent('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . urlencode($ip));
                if (!empty($cityQueryRet)) {
                    $country = empty($cityQueryRet['country']) ? '' : $cityQueryRet['country'];
                    $province = empty($cityQueryRet['province']) ? '' : $cityQueryRet['province'];
                    $city = empty($cityQueryRet['city']) ? '' : $cityQueryRet['city'];
                    if ($country || $province || $city) {
                        $cached = [
                            'country' => $country,
                            'province' => $province,
                            'city' => $city,
                        ];
                        return $cached;
                    }
                }
                break;
            case 2:
                $cityQueryRet = self::getContent("http://ip.taobao.com/service/getIpInfo.php?ip=" . urlencode($ip));
                $cityQuery = @json_decode($cityQueryRet, true);
                if (isset($cityQuery['code']) && $cityQuery['code'] == 0) {
                    $country = empty($cityQuery['data']['country']) ? '' : $cityQuery['data']['country'];
                    $province = empty($cityQuery['data']['region']) ? '' : $cityQuery['data']['region'];
                    $city = empty($cityQuery['data']['city']) ? '' : $cityQuery['data']['city'];
                    if ($country || $province || $city) {
                        $cached = [
                            'country' => $country,
                            'province' => $province,
                            'city' => $city,
                        ];
                        return $cached;
                    }
                }
                break;
            case 3:
                $cityQueryRet = self::getContent("http://freeapi.ipip.net/" . urlencode($ip));
                $cityQuery = @json_decode($cityQueryRet, true);
                if (isset($cityQuery[0])) {
                    $country = empty($cityQuery[0]) ? '' : $cityQuery[0];
                    $province = empty($cityQuery[1]) ? '' : $cityQuery[1];
                    $city = empty($cityQuery[2]) ? '' : $cityQuery[2];
                    if ($country || $province || $city) {
                        $cached = [
                            'country' => $country,
                            'province' => $province,
                            'city' => $city,
                        ];
                        return $cached;
                    }
                }
                break;
        }
        return null;
    }

    private static function getContent($url)
    {
        return @file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 1
            ]
        ]));
    }
}

