<?php

namespace Edwin404\Tecmz\Service;

use Edwin404\Base\Support\ModelHelper;
use Illuminate\Support\Facades\Cache;

class AdService
{
    const CACHE_KEY_PREFIX = 'tecmz.ad.';

    public function listByPosition($position)
    {
        return ModelHelper::model('ad')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    public function listByPositionWithCache($position = 'home', $minutes = 60)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public function clearCache($position)
    {
        Cache::forget(self::CACHE_KEY_PREFIX . $position);
    }
}