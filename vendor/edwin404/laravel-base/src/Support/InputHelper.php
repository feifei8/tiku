<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Facades\Input;

class InputHelper
{


    /**
     * @param $fieldConfig array see @example
     * @return array [code=>0,msg=>null,data=>[field1=>value2,field2=>value2,...]]
     *
     * @example
    field1=>[inputPoolKey,用户名,string|int|float|decimal|type,trim,min:4,max:20,required]
     *
     * @example
     */
    /*
     $data = InputPackage::buildFromInputJson('data');
     $ret = InputHelper::get([
        'title' => ['title', '套餐名称', 'string', 'trim', 'required'],
        'price' => ['title', '价格', 'decimal', 'trim', 'required'],
        'saleStatus' => ['title', '状态', 'type', GoodsSaleStatus::class, 'required'],
        'shippingPrice' => ['title', '邮费', 'decimal', 'trim',],
     ], $data->all());
     if ($ret['code']) {
        return Response::send(-1, $ret['msg']);
     }
     $package = $ret['data'];
     */
    public static function get($fieldConfig, $inputPool = null)
    {
        $data = [];

        if (null === $inputPool) {
            $inputPool = Input::all();
        }

        foreach ($fieldConfig as $key => $rule) {

            $poolKey = $rule[0];
            $name = $rule[1];
            $type = $rule[2];

            $data[$key] = (isset($inputPool[$poolKey]) ? $inputPool[$poolKey] : null);

            $ruleStart = 3;
            switch ($type) {
                case 'int':
                    $data[$key] = intval($data[$key]);
                    break;
                case 'float':
                    $data[$key] = floatval($data[$key]);
                    break;
                case 'decimal':
                    $data[$key] = @bcdiv(bcmul($data[$key], 100, 2), 100, 2);
                    if (is_null($data[$key])) {
                        $data[$key] = 0;
                    }
                    break;
                case 'type':
                    $data[$key] = trim($data[$key]);
                    if (!TypeHelper::name($rule[$ruleStart], $data[$key])) {
                        $data[$key] = null;
                    }
                    $ruleStart++;
                    break;
                case 'string':
                    break;
            }
            $ruleCount = count($rule);
            for ($i = $ruleStart; $i < $ruleCount; $i++) {
                $args = explode(':', $rule[$i]);
                switch ($args[0]) {
                    case 'trim':
                        $data[$key] = trim($data[$key]);
                        break;
                    case 'reg':
                        if (!preg_match($args[1], $data[$key])) {
                            return Response::generate(-1, $name . '格式错误');
                        }
                        break;
                    case 'min':
                        switch ($type) {
                            case 'int':
                                if ($data[$key] < intval($args[1])) {
                                    return Response::generate(-1, $name . '最小为' . $args[1]);
                                }
                                break;
                            case 'float':
                                if ($data[$key] < floatval($args[1])) {
                                    return Response::generate(-1, $name . '最小为' . $args[1]);
                                }
                                break;
                            default:
                                if (strlen($data[$key]) < intval($args[1])) {
                                    return Response::generate(-1, $name . '最小长度为' . $args[1]);
                                }
                        }
                        break;
                    case 'max':
                        switch ($type) {
                            case 'int':
                                if ($data[$key] > intval($args[1])) {
                                    return Response::generate(-1, $name . '最大为' . $args[1]);
                                }
                                break;
                            case 'float':
                                if ($data[$key] > floatval($args[1])) {
                                    return Response::generate(-1, $name . '最大为' . $args[1]);
                                }
                                break;
                            default:
                                if (strlen($data[$key]) > intval($args[1])) {
                                    return Response::generate(-1, $name . '最大长度为' . $args[1]);
                                }
                        }
                        break;
                    case 'required':
                        if (empty($data[$key])) {
                            return Response::generate(-1, $name . '不能为空');
                        }
                        break;
                }
            }

        }

        return Response::generate(0, null, $data);
    }

    public static function getJsonArray($key, $defaultValue = [])
    {
        $data = Input::get($key);
        if (empty($data)) {
            return $defaultValue;
        }
        $data = @json_decode($data, true);
        if (empty($data)) {
            return $defaultValue;
        }
        return $data;
    }

    public static function getArray($key, $defaultValue = [])
    {
        $data = Input::get($key, []);
        if (empty($data)) {
            return $defaultValue;
        }
        if (!is_array($data)) {
            return $defaultValue;
        }
        return $data;
    }

    public static function getJson($key, $defaultValue = null)
    {
        $data = Input::get($key, '');
        $data = @json_decode($data, true);
        if (empty($data)) {
            return $defaultValue;
        }
        return $data;
    }

    public static function getType($key, $typeClass, $defaultValue = null)
    {
        $data = Input::get($key, null);
        if (empty($data)) {
            return $defaultValue;
        }
        $list = $typeClass::getList();
        foreach ($list as $k => $v) {
            if ($data == $k) {
                return $k;
            }
        }
        return $defaultValue;
    }

    public static function getDate($key, $defaultValue = null)
    {
        $data = Input::get($key, null);
        if (empty($data)) {
            if (empty($defaultValue)) {
                return null;
            }
            return date('Y-m-d', strtotime($defaultValue));
        }
        if (DatetimeHelper::isDatetimeEmpty($data)) {
            if (empty($defaultValue)) {
                return null;
            }
            return date('Y-m-d', strtotime($defaultValue));
        }
        return date('Y-m-d', strtotime($data));
    }

    public static function getMonth($key, $defaultValue = null)
    {
        $data = Input::get($key, null);
        if (empty($data)) {
            if (empty($defaultValue)) {
                return null;
            }
            return date('Y-m', strtotime($defaultValue));
        }
        if (DatetimeHelper::isDatetimeEmpty($data)) {
            if (empty($defaultValue)) {
                return null;
            }
            return date('Y-m', strtotime($defaultValue));
        }
        return date('Y-m', strtotime($data));
    }

}