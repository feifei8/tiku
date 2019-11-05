<?php

namespace Edwin404\Tecmz\Traits;


use Edwin404\Config\Facades\ConfigFacade;

trait PayConfigTrait
{
    public function bootPayConfig()
    {
        // 初次安装时候无数据库信息下面代码会报错
        try {

            if (env('PAY_ALIPAY_ON', false)) {
                $this->app->config->set('pay.alipay', [
                    'partnerId' => env('PAY_ALIPAY_PARTNER_ID', null),
                    'sellerId' => env('PAY_ALIPAY_SELLER_ID', null),
                    'key' => env('PAY_ALIPAY_KEY', null),
                ]);
            } else if (ConfigFacade::get('payAlipayOn', false)) {
                $this->app->config->set('pay.alipay', [
                    'partnerId' => ConfigFacade::get('payAlipayPartnerId'),
                    'sellerId' => ConfigFacade::get('payAlipaySellerId'),
                    'key' => ConfigFacade::get('payAlipayKey'),
                ]);
            }

        } catch (\Exception $e) {
            //Do Nothing
        }
    }
}