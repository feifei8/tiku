<?php

namespace EdwinFound\Utils;


class FileUtil
{
    public static function mime($type)
    {
        static $mimeMap = [
            'png' => 'image/png',
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];
        $type = strtolower($type);
        return isset($mimeMap[$type]) ? $mimeMap[$type] : null;
    }

    public static function extension($pathname)
    {
        return strtolower(pathinfo($pathname, PATHINFO_EXTENSION));
    }
}