<?php

namespace Edwin404\Tecmz\Api;


use Edwin404\Base\Support\CurlHelper;
use Edwin404\Base\Support\Response;

/**
 * Class TecmzApiRequest
 * @package Edwin404\Tecmz\Api
 *
 * @deprecated
 */
class TecmzApiRequest
{
    const API_BASE = 'http://api.tecmz.com/api';

    private $appId;

    public function __construct($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @param $appId
     * @return TecmzApiRequest
     */
    public static function instance($appId)
    {
        static $map = [];
        if (!isset($map[$appId])) {
            $map[$appId] = new TecmzApiRequest($appId);
        }
        return $map[$appId];
    }

    private function request($gate, $param = [])
    {
        $param['app_id'] = $this->appId;
        return CurlHelper::getStandardJson(self::API_BASE . $gate, $param);
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
     * 参考 http://api.tecmz.com/api_doc/2
     */
    public function news($count = 1, $tag = '')
    {
        return $this->request('/news', [
            'count' => $count,
            'tag' => $tag,
        ]);
    }

}