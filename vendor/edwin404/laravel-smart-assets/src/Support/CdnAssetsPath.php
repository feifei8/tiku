<?php

namespace Edwin404\SmartAssets\Support;


use Edwin404\SmartAssets\Contracts\AssetsPath;
use Illuminate\Support\Facades\Cache;

class CdnAssetsPath implements AssetsPath
{
    const CACHE_PREFIX = 'smart-assets-file:';

    public function getPathWithHash($file)
    {
        $hash = Cache::get($flag = self::CACHE_PREFIX . $file, null);
        if (null !== $hash) {
            return $file . '?' . $hash;
        }
        if (file_exists($file)) {
            $hash = 'v=' . substr(md5_file($file), 0, 8);
            Cache::put($flag, $hash, 0);
            return $file . '?' . $hash;
        }
        Cache::put($flag, '', 0);
        return $file;
    }

    public function getCDN($file)
    {
        $cdnArray = config('smart-assets.assets_cdn_array', ['/']);
        $cdnIndex = abs(crc32($file) % count($cdnArray));
        return $cdnArray[$cdnIndex];
    }
}