<?php

namespace Edwin404\Tecmz\Traits;

use Edwin404\Base\Support\ModelHelper;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

trait DomainCityTrait
{
    protected $city = null;
    protected $cityDomain = null;
    protected $mainDomain = null;

    // 尝试获取城市优先级：
    // 1. 根据用户的Cookie获取
    // 2. 根据IP定位城市
    // 3. 默认城市
    protected function cityAwareSetup()
    {
        $this->mainDomain = env('DOMAIN_MAIN');

        // 1
        if (empty($city)) {
            if (!empty($cityCookie = Cookie::get('city'))) {
                $city = ModelHelper::load('area', ['shortEnName' => $cityCookie]);
            }
        }

        // 2
        if (empty($city)) {
            $ip = Request::ip();
            $cityQueryRet = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . urlencode($ip));
            if (!empty($cityQueryRet)) {
                $cityQuery = @json_decode($cityQueryRet, true);
                if (isset($cityQuery['code']) && $cityQuery['code'] == 0 && isset($cityQuery['data']['city']) && $cityQuery['data']['city']) {
                    $cityName = str_replace('市', '', $cityQuery['data']['city']);
                    $city = ModelHelper::load('area', ['pid' => 0, 'active' => 1, 'name' => $cityName]);
                }
            }
        }

        // 3
        if (empty($city)) {
            $city = ModelHelper::load('area', ['isDefault' => true, 'active' => true,]);
        }

        Cookie::queue('city', $city['shortEnName'], 365 * 24 * 60);

        $this->city = $city;
        $this->cityDomain = $city['shortEnName'] . '.' . $this->mainDomain;

        View::share('_city', $this->city);
        View::share('_cityDomain', $this->cityDomain);
        View::share('_mainDomain', $this->mainDomain);
    }

    // 城市的获取优先级:
    // 1. 根据URL包含的信息获取
    // 2. 根据用户的Cookie获取
    // 3. 根据IP定位城市
    // 4. 默认城市
    protected function citySetup()
    {
        $this->mainDomain = env('DOMAIN_MAIN');

        // 1
        $domain = Request::server('HTTP_HOST');
        if ($domain != $this->mainDomain) {
            $cityEnName = substr($domain, 0, strlen($domain) - strlen($this->mainDomain) - 1);
            $city = ModelHelper::load('area', ['shortEnName' => $cityEnName]);
            if (empty($city)) {
                header('Location: http://' . $this->mainDomain);
                exit();
            }
        }

        // 2
        if (empty($city)) {
            if (!empty($cityCookie = Cookie::get('city'))) {
                $city = ModelHelper::load('area', ['shortEnName' => $cityCookie]);
                if (empty($city)) {
                    header('Location: http://' . $this->mainDomain);
                    exit();
                }
            }
        }

        // 3
        if (empty($city)) {
            $ip = Request::ip();
            //$ip = Input::get('ip');
            $cityQueryRet = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . urlencode($ip));
            if (!empty($cityQueryRet)) {
                $cityQuery = @json_decode($cityQueryRet, true);
                if (isset($cityQuery['code']) && $cityQuery['code'] == 0 && isset($cityQuery['data']['city']) && $cityQuery['data']['city']) {
                    $cityName = str_replace('市', '', $cityQuery['data']['city']);
                    $city = ModelHelper::load('area', ['pid' => 0, 'active' => 1, 'name' => $cityName]);
                    //Log::info('通过定位找到了:' . print_r($city, true));
                }
            }
        }

        // 4
        if (empty($city)) {
            $city = ModelHelper::load('area', ['isDefault' => true, 'active' => true,]);
            if (empty($city)) {
                exit('必须设定一个默认城市');
            }
            if (empty($city)) {
                exit('默认城市必须为开启');
            }
        }

        if (empty($city['active'])) {
            header('Location: http://' . $this->mainDomain);
            exit();
        }

        Cookie::queue('city', $city['shortEnName'], 365 * 24 * 60);

        $this->city = $city;
        $this->cityDomain = $city['shortEnName'] . '.' . $this->mainDomain;

        View::share('_city', $this->city);
        View::share('_cityDomain', $this->cityDomain);
        View::share('_mainDomain', $this->mainDomain);
    }

    protected function city()
    {
        return $this->city;
    }

    protected function cityId()
    {
        return $this->city['id'];
    }

    protected function cityDomain()
    {
        return $this->cityDomain;
    }

}