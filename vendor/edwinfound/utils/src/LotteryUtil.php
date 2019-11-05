<?php

namespace EdwinFound\Utils;


class LotteryUtil
{
    /**
     * 抽取一个奖品
     *
     * @param array $pool = array(
     *  ['id'=>xx,'rate'=>5.01],
     *  ['id'=>xx,'rate'=>5.00],
     *  ['id'=>xx,'rate'=>5.00],
     * )
     * @return id or null
     * @throws \Exception
     */
    public static function fetchPoll(array $pool)
    {
        $map = [];
        $index = 0;
        foreach ($pool as $item) {
            $space = intval(bcmul($item['rate'], 100, 2));
            if ($space <= 0) {
                continue;
            }
            for ($i = 0; $i < $space; $i++) {
                $map[$index++] = $item['id'];
            }
            if ($index > 10000) {
                throw new \Exception('bad lottery pool 10000');
            }
        }
        while ($index < 10000) {
            $map[$index++] = null;
        }
        $index = rand(0, 9999);
        return $map[$index];
    }

    /**
     * 返回一个取值范围（包含 min 和 max）
     *
     * @param $min : 0.00
     * @param $max : 1.00
     * @return string
     */
    public static function randomMoneyInRange($min, $max)
    {
        $redbagValue = rand(
            intval(bcmul($min, 100, 2)),
            intval(bcmul($max, 100, 2))
        );
        return bcdiv($redbagValue, 100, 2);
    }

}