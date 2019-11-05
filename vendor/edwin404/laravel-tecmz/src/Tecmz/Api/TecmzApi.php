<?php

namespace Edwin404\Tecmz\Api;


use Edwin404\Base\Support\CurlHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Base\Support\SignHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TecmzApi
{
    const API_BASE = 'http://api.tecmz.com/api';

    private $appId;
    private $appSecret;

    private $debug = false;

    public function __construct($appId, $appSecret = null)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * @param $appId
     * @param $appSecret
     * @return TecmzApi
     */
    public static function instance($appId, $appSecret = null)
    {
        static $map = [];
        if (!isset($map[$appId])) {
            $map[$appId] = new self($appId, $appSecret);
        }
        return $map[$appId];
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    private function request($gate, $param = [])
    {
        $param['app_id'] = $this->appId;
        if ($this->appSecret) {
            $param['timestamp'] = time();
            $param['sign'] = SignHelper::common($param, $this->appSecret);
        }
        if ($this->debug) {
            Log::debug('TecmzAPI -> ' . self::API_BASE . $gate . ' -> ' . json_encode($param));
        }
        return CurlHelper::postStandardJson(self::API_BASE . $gate, $param);
    }

    /**
     * 测试接口连通性
     * @return array
     *
     * 错误 [code=>-1,msg=>'<错误原因>',data=>null]
     * 正确 [code=>0,msg=>'ok',data=>null]
     */
    public function ping()
    {
        $ret = $this->request('/ping');
        if ($ret['code']) {
            return Response::generate(-1, 'PING失败');
        }
        return Response::generate(0, 'ok');
    }

    /**
     * 获取资讯新闻
     *
     * @param int $count
     * @param string $tag
     * @return array
     *
     * 错误 [code=>-1,msg=>'<错误原因>',data=>null]
     * 正确 [code=>0,msg=>'ok',data=>['list'=>[...]]]
     */
    public function news($count = 1, $tag = '')
    {
        return $this->request('/news', [
            'count' => $count,
            'tag' => $tag,
        ]);
    }

    /**
     * 查询快递
     *
     * @param $type
     * @param $no
     * @param int $cacheMinutes
     * @return array
     *
     * 错误 [code=>-1,msg=>'<错误原因>',data=>null]
     * 正确 [code=>0,msg=>ok',data=>[list=>[time=>xxx,text=>xxx]]]
     */
    public function express($type, $no, $cacheMinutes = 5)
    {
        $flag = json_encode([
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'api' => 'express',
            'type' => $type,
            'no' => $no,
        ]);
        if ($cacheMinutes > 0) {
            return Cache::remember($flag, $cacheMinutes, function () use ($type, $no) {
                return $this->request('/express', [
                    'type' => $type,
                    'no' => $no,
                ]);
            });
        } else {
            return $this->request('/express', [
                'type' => $type,
                'no' => $no,
            ]);
        }
    }

    /**
     * 语音转换
     *
     * @param string $from
     * @param string $to
     * @param string $rawContent
     * @return array
     *
     * 错误 [code=>-1,msg=>'<错误原因>',data=>null]
     * 正确 [code=>0,msg=>'ok',data=>['list'=>[...]]]
     */
    public function audioConvert($from, $to, $rawContent)
    {
        $ret = $this->request('/audio_convert', [
            'from' => $from,
            'to' => $to,
            'content' => base64_encode($rawContent),
        ]);
        if (!empty($ret['data']['content'])) {
            $ret['data']['content'] = @base64_decode($ret['data']['content']);
            if (empty($ret['data']['content'])) {
                return Response::generate(-1, 'audio convert error');
            }
        }
        return $ret;
    }

    /**
     * 语音识别
     *
     * @param string $type : amr, wav
     * @param string $rawContent
     * @return array
     *
     * 错误 [code=>-1,msg=>'<错误原因>',data=>null]
     * 正确 [code=>0,msg=>'ok',data=>['text'=>...]]
     */
    public function asr($type, $rawContent)
    {
        return $this->request('/asr', [
            'type' => $type,
            'content' => base64_encode($rawContent),
        ]);
    }

}