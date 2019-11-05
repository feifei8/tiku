<?php

namespace Edwin404\Base\Support;


class TreeHelper
{
    static $CHILD_KEY = '_child';

    public static function setChildKey($key)
    {
        self::$CHILD_KEY = $key;
    }

    /**
     * @param $model
     * @param array $fieldsMap = [title=>titleField,...]
     * @return array
     */
    public static function model2Nodes($model, $fieldsMap = [], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort')
    {
        $models = ModelHelper::find($model);
        $nodes = [];
        foreach ($models as &$model) {
            $node = [];
            $node[$keyId] = $model[$keyId];
            $node[$keyPid] = $model[$keyPid];
            $node[$keySort] = $model[$keySort];
            foreach ($fieldsMap as $k => $v) {
                $node[$k] = $model[$v];
            }
            $nodes[] = $node;
        }
        return self::nodeMerge($nodes, 0, $keyId, $keyPid, $keySort);
    }

    public static function model2NodesByParentId($pid, $model, $fieldsMap = [], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort')
    {
        $models = [];

        $m = ModelHelper::load($model, [$keyId => $pid]);
        if (empty($m)) {
            return [];
        }
        $topPid = $m[$keyPid];
        $models[] = $m;

        $ms = ModelHelper::find($model, [$keyPid => $pid]);
        do {
            $parentIds = [];
            foreach ($ms as &$m) {
                $parentIds[] = $m[$keyId];
                $models[] = $m;
            }
            if (empty($parentIds)) {
                $ms = null;
            } else {
                $ms = ModelHelper::model($model)->whereIn($keyPid, $parentIds)->get()->toArray();
            }
        } while (!empty($ms));

        $nodes = [];
        foreach ($models as &$model) {
            $node = [];
            $node[$keyId] = $model[$keyId];
            $node[$keyPid] = $model[$keyPid];
            $node[$keySort] = $model[$keySort];
            foreach ($fieldsMap as $k => $v) {
                $node[$k] = $model[$v];
            }
            $nodes[] = $node;
        }
        return self::nodeMerge($nodes, $topPid, $keyId, $keyPid, $keySort);
    }

    // 如果有子节点则不能删除
    public static function modelNodeDeleteAble($model, $id, $pidKey = 'pid')
    {
        return !ModelHelper::exists($model, [$pidKey => $id]);
    }

    public static function modelNodeChangeAble($model, $id, $fromPid, $toPid, $idKey = 'id', $pidKey = 'pid')
    {
        if ($fromPid == $toPid) {
            return true;
        }

        $_toPid = $toPid;

        while ($m = ModelHelper::load($model, [$idKey => $_toPid])) {
            if ($m[$idKey] == $id) {
                return false;
            }
            $_toPid = $m[$pidKey];
        }

        return true;
    }

    public static function nodeMerge(&$node, $pid = 0, $pk_name = 'id', $pid_name = 'pid', $sort_name = 'sort', $sort_direction = 'asc')
    {
        // 下面是使用直接循环
        self::arraySortByKey($node, $sort_name, $sort_direction);
        $items = [];
        foreach ($node as $v) {
            $items[$v[$pk_name]] = $v;
        }
        $tree = [];
        foreach ($items as $item) {
            if (!isset($items[$item[$pk_name]][self::$CHILD_KEY])) {
                $items[$item[$pk_name]][self::$CHILD_KEY] = [];
            }
            if (isset($items[$item[$pid_name]])) {
                $items[$item[$pid_name]][self::$CHILD_KEY][] = &$items[$item[$pk_name]];
            } else {
                $tree[] = &$items[$item[$pk_name]];
            }
        }
        return $tree;

        // 下面是使用递归的算法
        //foreach ($node as &$v) {
        //    if ($v [$pid_name] == $pid) {
        //        $v [self::$CHILD_KEY] = self::nodeMerge($node, $v [$pk_name], $pk_name, $pid_name, $sort_name, $sort_direction);
        //        $arr [] = $v;
        //    }
        //}
        //self::arraySortByKey($arr, $sort_name, $sort_direction);

        return $arr;
    }

    public static function arraySortByKey(&$arr, $key, $order = 'asc|desc')
    {
        if ($order == 'desc') {
            $order = '>';
        } else {
            $order = '<';
        }
        $cmp_func = create_function('$a,$b', '
		if($a["' . $key . '"]  ==  $b["' . $key . '"])  return  0;
		return  $a["' . $key . '"]' . $order . '$b["' . $key . '"]?-1:1;
		');
        usort($arr, $cmp_func);
    }

    public static function listIndent(&$list, $keyId, $keyTitle, $level = 0)
    {
        $options = array();
        foreach ($list as &$r) {
            $options[] = array('id' => $r[$keyId], 'title' => str_repeat('|---', $level) . htmlspecialchars($r[$keyTitle]));
            if (!empty($r[self::$CHILD_KEY])) {
                $options = array_merge($options, self::listIndent($r[self::$CHILD_KEY], $keyId, $keyTitle, $level + 1));
            }
        }
        return $options;
    }

    public static function allChildIds(&$list, $id, $pk_name = 'id', $pid_name = 'pid')
    {
        $ids = [];
        foreach ($list as &$li) {
            if ($li[$pid_name] == $id) {
                $ids[] = $li[$pk_name];
                $childIds = self::allChildIds($list, $li[$pk_name], $pk_name, $pid_name);
                if (!empty($childIds)) {
                    $ids = array_merge($ids, $childIds);
                }
            }
        }
        return $ids;
    }

    public static function chain(&$list, $id, $pk_name = 'id', $pid_name = 'pid')
    {
        $chain = [];
        $limit = 0;
        $found = true;
        while ($found && $limit++ < 999) {
            $found = false;
            foreach ($list as $li) {
                if ($li[$pk_name] == $id) {
                    $found = true;
                    $id = $li[$pid_name];
                    $chain[] = $li;
                    break;
                }
            }
        }
        return array_reverse($chain);
    }

}