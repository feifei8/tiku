<?php

namespace App\Helpers;


use Edwin404\Base\Support\HtmlHelper;

class QuestionHelper
{

    public static function hasContent($text)
    {
        $text = trim(HtmlHelper::text($text));
        return !empty($text);
    }
}