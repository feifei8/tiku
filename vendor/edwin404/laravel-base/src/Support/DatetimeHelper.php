<?php

namespace Edwin404\Base\Support;


class DatetimeHelper
{
    /**
     * 判断一个日期时间是否为空
     * 经常会出现 0000-00-00 00:00:00 的日期,这样判断就不为空,会发生误判
     *
     * @param $datetime
     *
     * @return boolean
     */
    public static function isDatetimeEmpty($datetime)
    {
        $timestamp = strtotime($datetime);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }

    /**
     * 判断一个日期时间是否为空
     * 经常会出现 0000-00-00 的日期,这样判断就不为空,会发生误判
     *
     * @param $date
     *
     * @return boolean
     */
    public static function isDateEmpty($date)
    {
        $timestamp = strtotime($date);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }

}