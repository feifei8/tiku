<?php

namespace Edwin404\Base\Support;

use Illuminate\Support\Facades\Log;

class SoundHelper
{
    public static function convertAMR2MP3($amrFileContent, $convertServer = null)
    {
        if (null === $convertServer) {
            $convertServer = env('SOUND_CONVERT_SERVER');
        }
        if (empty($convertServer)) {
            return Response::generate(-1, 'Sound Convert Server Empty');
        }
        if (empty($amrFileContent)) {
            return Response::generate(-1, 'amr file empty');
        }
        $post_data = array("from" => "amr", "to" => "mp3", "content" => $amrFileContent);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $convertServer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        curl_close($ch);
        if (empty($output)) {
            return Response::generate(-1, '转换声音文件失败');
        }
        return Response::generate(0, null, $output);
    }

    public static function convertAMR2WAV($amrFileContent, $convertServer = null)
    {
        if (null === $convertServer) {
            $convertServer = env('SOUND_CONVERT_SERVER');
        }
        if (empty($convertServer)) {
            return Response::generate(-1, 'Sound Convert Server Empty');
        }
        $post_data = array("from" => "amr", "to" => "wav", "content" => $amrFileContent);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $convertServer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        curl_close($ch);
        if (empty($output)) {
            return Response::generate(-1, '声音文件失败');
        }
        return Response::generate(0, null, $output);
    }

    /**
     * 识别语音
     *
     * @param $wav8KFileContent
     * @param null $aliyunAccessKey
     * @param null $aliyunAccessSecret
     * @return array|mixed
     */
    public static function recognizeWAV($wav8KFileContent, $aliyunAccessKey = null, $aliyunAccessSecret = null)
    {
        if (null === $aliyunAccessKey) {
            $aliyunAccessKey = env('SOUND_ALIYUN_ACCESS_KEY');
        }
        if (null === $aliyunAccessSecret) {
            $aliyunAccessSecret = env('SOUND_ALIYUN_ACCESS_SECRET');
        }

        $url = "https://nlsapi.aliyun.com/recognize?model=customer-service-8k&version=2.0";
        $method = 'POST';
        $contentType = 'audio/wav; samplerate=8000';
        $date = gmdate("D, d M Y H:i:s \\G\\M\\T");
        $accept = 'application/json';

        $bodyMD5 = base64_encode(md5($wav8KFileContent, true));
        $bodyMD5 = base64_encode(md5($bodyMD5, true));
        $stringToSign = "$method\n$accept\n$bodyMD5\n$contentType\n$date";
        $signature = base64_encode(hash_hmac("sha1", $stringToSign, $aliyunAccessSecret, true));

        $sendHeaders = [];
        $sendHeaders[] = "Authorization: Dataplus $aliyunAccessKey:$signature";
        $sendHeaders[] = "Content-type: " . $contentType;
        $sendHeaders[] = "Accept: " . $accept;
        $sendHeaders[] = "Date: " . $date;
        $sendHeaders[] = "Content-Length: " . strlen($wav8KFileContent);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $wav8KFileContent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($output, true);
        if (!empty($result['result'])) {
            return Response::generate(0, null, $result['result']);
        }
        return Response::generate(-1, '声音识别错误');
    }

}