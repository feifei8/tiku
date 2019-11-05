<?php

namespace Edwin404\Base\Support;


use Edwin404\SmartAssets\Helper\AssetsHelper;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class MarkdownHelper
{
    public static function convertToHtml($markdown)
    {
        $converter = new CommonMarkConverter([
            'renderer' => [
                'soft_break' => "<br />",
            ],
        ]);
        return $converter->convertToHtml($markdown);
    }

    public static function replaceImageSrcToCDN($content, $dataAttr = 'data-src')
    {
        $currentDomainUrl = RequestHelper::domainUrl();
        preg_match_all('/!\\[(.*?)\\]\\((.*?)\\)/i', $content, $mat);
        foreach ($mat[0] as $k => $v) {
            $imageUrl = $mat[2][$k];
            if (Str::startsWith($imageUrl, $currentDomainUrl)) {
                $imageUrl = substr($mat[2][$k], strlen($currentDomainUrl));
                $content = str_replace($v, '![' . $mat[1][$k] . '](' . AssetsHelper::fix($imageUrl) . ')', $content);
            }
        }
        return $content;
    }

}