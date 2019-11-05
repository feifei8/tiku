<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Config\Facades\ConfigFacade;
use Illuminate\Support\Facades\Mail;

class MailHelper
{

    public static function send($email, $subject, $template, $templateData = [], $emailUserName = null, $option = [])
    {
        if (!ConfigFacade::get('systemEmailEnable')) {
            return;
        }

        static $inited = false;
        if (!$inited) {
            $inited = true;
            config([
                'mail' => [
                    'driver' => 'smtp',
                    'host' => ConfigFacade::get('systemEmailSmtpServer'),
                    'port' => ConfigFacade::get('systemEmailSmtpSsl', false) ? 465 : 25,
                    'encryption' => ConfigFacade::get('systemEmailSmtpSsl', false) ? 'ssl' : 'tls',
                    'from' => array('address' => ConfigFacade::get('systemEmailSmtpUser'), 'name' => ConfigFacade::get('siteName') . ' @ ' . ConfigFacade::get('siteDomain')),
                    'username' => ConfigFacade::get('systemEmailSmtpUser'),
                    'password' => ConfigFacade::get('systemEmailSmtpPassword'),
                ]
            ]);
        }

        $view = 'theme.' . ConfigFacade::get('siteTemplate', 'default') . '.mail.' . $template;
        if (!view()->exists($view)) {
            $view = 'theme.default.mail.' . $template;
            if (!view()->exists($view)) {
                $view = 'tecmz::mail.' . $template;
            }
        }

        if (!view()->exists($view)) {
            throw new \Exception('mail view not found : ' . $view);
        }

        if (null === $emailUserName) {
            $emailUserName = $email;
        }

        Mail::send($view, $templateData, function ($message) use ($email, $emailUserName, $subject, $option) {
            $message->to($email, $emailUserName)->subject($subject);
            if (!empty($option['attachment'])) {
                foreach ($option['attachment'] as $filename => $path) {
                    $message->attach($path, ['as' => $filename]);
                }
            }
        });
    }
}