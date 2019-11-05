<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Str;

class EncryptHelper
{
    public static function expiredData($string, $key, $expireSeconds = 60)
    {
        $stringHex = bin2hex($string);
        $nonce = strtolower(Str::random(6));
        $timestampHex = dechex(time());
        $expireSecondsHex = dechex($expireSeconds);
        $sign = substr(md5($key . $stringHex . $nonce . $timestampHex . $expireSecondsHex), 0, 6);
        $param = [];
        $param[] = $stringHex;
        $param[] = $nonce;
        $param[] = $timestampHex;
        $param[] = $expireSecondsHex;
        $param[] = $sign;
        return join('_', $param);
    }

    public static function expiredDataDecode($url, $key)
    {
        $p = explode('_', $url);
        if (count($p) != 5) {
            return null;
        }
        $stringHex = $p[0];
        $nonce = $p[1];
        $timestampHex = $p[2];
        $expireSecondsHex = $p[3];
        $sign = $p[4];
        $signCalc = substr(md5($key . $stringHex . $nonce . $timestampHex . $expireSecondsHex), 0, 6);
        if ($sign != $signCalc) {
            return null;
        }
        $expireSeconds = hexdec($expireSecondsHex);
        if ($expireSeconds > 0) {
            $timestamp = hexdec($timestampHex);
            if (time() > $timestamp + $expireSeconds) {
                return null;
            }
        }
        return hex2bin($stringHex);
    }

    public static function encode($string, $key = '', $expiry = 0)
    {
        return self::authcode($string, 'ENCODE', $key, $expiry);
    }

    public static function decode($string, $key = '')
    {
        return self::authcode($string, 'DECODE', $key);
    }

    private static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;

        $key = md5($key ? $key : 'RandomKey');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }
}