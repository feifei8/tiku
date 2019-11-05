<?php

namespace Edwin404\Tecmz\Helpers;

class MonitorHelper
{
    public static function notify($url, $data)
    {
        if (empty($url)) {
            return;
        }
        @file_get_contents($url . '?data=' . urlencode(json_encode($data)));
    }
}