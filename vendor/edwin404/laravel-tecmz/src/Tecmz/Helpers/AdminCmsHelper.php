<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\TypeHelper;
use Edwin404\SmartAssets\Helper\AssetsHelper;

class AdminCmsHelper
{
    public static function thumbViewItem($image)
    {
        $url = AssetsHelper::fixOrDefault($image, 'assets/lib/img/none.png');
        return '<a href="' . $url . '" style="border:1px solid #CCC;display:inline-block;height:42px;width:42px;box-sizing:border-box;border-radius:2px;" data-image-preview><img src="' . $url . '" style="height:40px;width:40px;display:inline-block;" /></a>';
    }

    public static function memberUserId($memberUserId)
    {
        $memberUser = ModelHelper::loadWithCache('member_user', ['id' => $memberUserId]);
        return self::memberUser($memberUser);
    }

    public static function memberUser($memberUser)
    {
        if (!empty($memberUser)) {
            return '<a href="javascript:;" data-dialog-request="' . action('\App\Http\Controllers\Admin\MemberController@dataView', ['id' => $memberUser['id']]) . '" class="list-member-user"><img src="'
                . AssetsHelper::fixOrDefault($memberUser['avatar'], 'assets/lib/img/avatar.png') . '" /><span>' . htmlspecialchars($memberUser['username']) . '</span></a>';
        }
        return '';
    }

    public static function userId($userId)
    {
        $user = ModelHelper::loadWithCache('user', ['id' => $userId]);
        return self::user($user);
    }

    public static function user($user)
    {
        if (!empty($user)) {
            return '<a href="javascript:;" data-dialog-request="' . action('\App\Http\Controllers\Admin\UserController@dataView', ['_id' => $user['id']]) . '" class="list-member-user"><img src="'
                . AssetsHelper::fixOrDefault($user['avatar'], 'assets/lib/img/avatar.png') . '" /><span>' . htmlspecialchars($user['username']) . '</span></a>';
        }
        return '';
    }

    public static function colorText($typeValue, $typeClass, $colorMap = [])
    {
        $text = htmlspecialchars(TypeHelper::name($typeClass, $typeValue));
        if (empty($colorMap[$typeValue])) {
            return $text;
        }
        return
            '<span class="uk-text-' . (empty($colorMap[$typeValue]) ? 'default' : $colorMap[$typeValue]) . '">'
            . $text . '</span>';
    }

    public static function colorBadge($typeValue, $typeClass, $colorMap = [])
    {
        $text = htmlspecialchars(TypeHelper::name($typeClass, $typeValue));
        if (empty($colorMap[$typeValue])) {
            return $text;
        }
        return
            '<span class="uk-badge uk-badge-' . (empty($colorMap[$typeValue]) ? 'default' : $colorMap[$typeValue]) . '">'
            . $text . '</span>';
    }

    public static function retry($link = null)
    {
        return '<a class="btn uk-button-danger" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="重试" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-refresh"></i></a>';
    }

    public static function pass($link = null)
    {
        return '<a class="btn uk-button-success" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="通过" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-check"></i></a>';
    }

    public static function reject($link = null)
    {
        return '<a class="btn uk-button-danger" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="拒绝" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-remove"></i></a>';
    }

    public static function repair($link = null)
    {
        return '<a class="btn uk-button-danger" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="修复" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-wrench"></i></a>';
    }

    public static function dialog($text, $link)
    {
        return '<a href="javascript:;" data-dialog-request="' . htmlspecialchars($link) . '">' . $text . '</a>';
    }

    public static function successText($text)
    {
        return '<span class="uk-text-success">' . $text . '</span>';
    }

    public static function dangerText($text)
    {
        return '<span class="uk-text-danger">' . $text . '</span>';
    }

    public static function warningText($text)
    {
        return '<span class="uk-text-warning">' . $text . '</span>';
    }

}