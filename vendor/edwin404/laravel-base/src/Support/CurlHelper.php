<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Str;

class CurlHelper
{
    public static function postContent($url, $param = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        if (Str::startsWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


    public static function postStandardJson($url, $param)
    {
        $content = self::postContent($url, $param);
        if (empty($content)) {
            return Response::generate(-1, '获取数据失败');
        }
        $contentJson = @json_decode($content, true);
        if (!isset($contentJson['code'])) {
            return Response::generate(-1, '获取数据失败', $contentJson);
        }
        return $contentJson;
    }

    public static function getContent($url, $param = [])
    {
        if (!empty($param)) {
            $url = $url . '?' . http_build_query($param);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        if (Str::startsWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $temp = curl_exec($ch);
        curl_close($ch);
        return $temp;
    }

    public static function getContentWithHeaders($url, $param = [], $headers = [])
    {
        $sendHeaders = [];
        foreach ($headers as $k => $v) {
            $sendHeaders[] = "$k: $v";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($param));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        if (Str::startsWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $temp = curl_exec($ch);
        curl_close($ch);
        return $temp;
    }

    public static function getStandardJson($url, $param = [])
    {
        $content = self::getContent($url, $param);
        if (empty($content)) {
            return Response::generate(-1, '获取数据失败');
        }
        $contentJson = @json_decode($content, true);
        if (!isset($contentJson['code'])) {
            return Response::generate(-1, '获取数据失败', $contentJson);
        }
        return $contentJson;
    }

    public static function getHeaderAndContent($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        if (Str::startsWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $temp = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200') {
            return null;
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerString = substr($temp, 0, $headerSize);
        $body = substr($temp, $headerSize);
        curl_close($ch);

        $header = [];
        foreach (explode("\n", $headerString) as $line) {
            $line = trim($line);
            if (preg_match('/^(.*?):(.*?)$/', $line, $mat)) {
                $header[] = [
                    strtolower(trim($mat[1])) => trim($mat[2])
                ];
            }
        }

        return [
            'header' => $header,
            'body' => $body,
        ];
    }
}