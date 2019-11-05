<?php

namespace EdwinFound\Utils;


class ArrayUtil
{
    public static function sequenceEqual($arr1, $arr2)
    {
        sort($arr1);
        sort($arr2);
        return json_encode($arr1) == json_encode($arr2);
    }

    public static function equal($arr1, $arr2, $keys = null, $strict = false)
    {
        if (null === $keys) {
            $keys = array_merge(array_keys($arr1), array_keys($arr2));
        }
        foreach ($keys as $k) {
            if (!array_key_exists($k, $arr1)) {
                return false;
            }
            if (!array_key_exists($k, $arr2)) {
                return false;
            }
            if ($strict) {
                if ($arr1[$k] !== $arr2[$k]) {
                    return false;
                }
            } else {
                if ($arr1[$k] != $arr2[$k]) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function fetchSpecifiedKeyToArray(&$arr, $key)
    {
        $r = [];
        foreach ($arr as $item) {
            $r[] = $item[$key];
        }
        return $r;
    }

    public static function filterSpecifiedKey(&$arr, $keys)
    {
        $newArr = [];
        if (empty($keys)) {
            return $newArr;
        }
        foreach ($arr as $k => $v) {
            if (in_array($k, $keys)) {
                $newArr[$k] = $v;
            }
        }
        return $newArr;
    }

    public static function pickRandomOne($arr)
    {
        if (empty($arr)) {
            return null;
        }
        return $arr[array_rand($arr)];
    }
}