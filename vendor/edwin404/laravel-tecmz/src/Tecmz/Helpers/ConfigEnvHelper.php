<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Config\Facades\ConfigFacade;

class ConfigEnvHelper
{
    public static function get($key, $defaultValue = null)
    {
        $value = ConfigFacade::get($key);
        if (empty($value)) {
            $value = env('CONFIG_' . $key);
        }
        if (empty($value)) {
            return $defaultValue;
        }
        return $value;
    }
}