<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Sms\Services\TecmzSmsService;
use Edwin404\Tecmz\Types\SmsSender;

class SmsHelper
{
    public static function send($phone, $type, $templateData = [])
    {
        if (!ConfigFacade::get('systemSmsEnable')) {
            return Response::generate(-1, '短信发送未开启');
        }

        $senderParam = [];

        switch ($type) {
            case 'verify':
                $senderParam['phone'] = $phone;
                $senderParam['tplId'] = ConfigFacade::get('systemSmsSenderTecmzVerifyTemplateId');
                $senderParam['param'] = $templateData;
                if (empty($senderParam['param']['verify'])) {
                    return Response::generate(-1, '短信模板缺少{verify}参数');
                }
                break;
            default:
                return Response::generate(-1, '未能识别的type');
        }

        switch (ConfigFacade::get('systemSmsSender')) {
            case SmsSender::TECMZ:
                $sender = new TecmzSmsService();
                return $sender->send(ConfigFacade::get('systemSmsSenderTecmzAppKey'), $senderParam['tplId'], $senderParam['phone'], $senderParam['param']);
            default:
                return Response::generate(-1, '未能识别的Sender');
        }

    }

}