<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Str;

class StrHelper
{
    public static function removeEmoji($text)
    {
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }

    public static function mask($subject, $startIndex = null, $endIndex = null, $maskChar = '*')
    {
        $strLen = strlen($subject);

        if (null == $startIndex) {
            $startIndex = floor($strLen / 2);
        }
        if (null == $endIndex) {
            $endIndex = $startIndex + floor($strLen / 2);
        }

        if ($startIndex < 0) {
            $startIndex = 0;
        }
        if ($endIndex >= $strLen - 1) {
            $endIndex = $strLen - 1;
        }

        $maskedSubject = '';
        if ($startIndex > 0) {
            $maskedSubject .= substr($subject, 0, $startIndex);
        }
        $maskedSubject .= str_repeat($maskChar, $endIndex - $startIndex + 1);
        if ($endIndex < $strLen - 1) {
            $maskedSubject .= substr($subject, $endIndex + 1);
        }
        return $maskedSubject;

    }

    public static function parseKey($generatedKey, $nonceStr = null)
    {
        if (empty($generatedKey)) {
            return null;
        }
        $generatedKey = explode('-', $generatedKey);
        if (count($generatedKey) != 3) {
            return null;
        }
        if ($nonceStr !== null) {
            if ($generatedKey[1] != $nonceStr) {
                return null;
            }
        }
        $secret = env('APP_KEY', '');
        if (substr(md5($secret . '-' . $generatedKey[0] . '-' . $generatedKey[1]), 0, 6) == $generatedKey[2]) {
            return $generatedKey[0];
        }
        return null;
    }

    public static function generateKey($key, $nonceStr = null)
    {
        if (null === $nonceStr) {
            $nonceStr = strtolower(Str::random(6));
        }
        $secret = env('APP_KEY', '');
        $sign = substr(md5($secret . '-' . $key . '-' . $nonceStr), 0, 6);
        return $key . '-' . $nonceStr . '-' . $sign;
    }

    /**
     * 下划线转驼峰
     * 思路:
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     */
    public static function camelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    /**
     * 驼峰命名转下划线命名
     * 思路:
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     */
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

}