<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;

class ShippingHelper
{
    public static function listStatus($type, $no)
    {
        if (!ConfigFacade::get('shippingStatusEnable', false)) {
            return Response::generate(-1, '物流信息未开启');
        }
        $appId = ConfigFacade::get('shippingStatusApiAppId');
        $shippingStatusApiAppSecret = ConfigFacade::get('shippingStatusApiAppSecret');
        return TecmzApiHelper::shipping($appId, $shippingStatusApiAppSecret, $type, $no);
    }
}