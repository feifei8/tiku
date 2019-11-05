<?php

namespace Edwin404\Admin\Cms\Helper;

use Edwin404\Base\Support\ModelHelper;

class CategoryCmsHelper
{

    public static function loadCategoryWithParents($model, $id, $keyId = 'id', $keyPid = 'pid')
    {
        $data = [];
        do {
            $item = ModelHelper::load($model, [$keyId => $id]);
            if (empty($item)) {
                break;
            }
            $data [] = $item;
            $id = $item[$keyPid];
        } while ($id != 0);
        return array_reverse($data);
    }

    public static function loadCategoryChildIds($model, $id, $keyId = 'id', $keyPid = 'pid')
    {
        $ids = [];
        $items = ModelHelper::find($model, [$keyPid => $id]);
        foreach ($items as &$item) {
            $id = $item[$keyId];
            $ids [] = $id;
            $ids = array_merge($ids, self::loadCategoryChildIds($model, $id, $keyId, $keyPid));
        }
        return $ids;
    }

    public static function loadCategoryChildren($model, $id, $keyId = 'id', $keyPid = 'pid')
    {
        return ModelHelper::find($model, [$keyPid => $id]);
    }

}