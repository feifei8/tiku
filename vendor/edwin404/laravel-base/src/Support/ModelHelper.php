<?php

namespace Edwin404\Base\Support;


use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModelHelper
{
    private static $timestampEnable = true;

    private static function isModel($model)
    {
        return strtolower($model) != $model;
    }

    public static function enableTimestamp($enable)
    {
        self::$timestampEnable = $enable;
    }

    public static function modelJoin(&$data, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($data)) {
            return;
        }

        $ids = [];
        foreach ($data as &$item) {
            $ids[$item[$dataModelKey]] = true;
        }

        $modelData = self::model($model)->whereIn($modelPrimaryKey, array_keys($ids))->get()->toArray();
        $modelDataMap = [];
        foreach ($modelData as &$r) {
            $modelDataMap[$r[$modelPrimaryKey]] = $r;
        }

        foreach ($data as &$item) {
            $key = $item[$dataModelKey];
            if (isset($modelDataMap[$key])) {
                $item[$dataMergedKey] = $modelDataMap[$key];
            } else {
                $item[$dataMergedKey] = null;
            }
        }
    }

    public static function load($model, $where)
    {
        if (self::isModel($model)) {
            $m = $model::where($where)->first();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            $m = $m->where($where)->first();
        }
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function loadWithCache($model, $where)
    {
        static $map = [];
        $flag = serialize(['model' => $model, 'where' => $where]);
        if (!array_key_exists($flag, $map)) {
            $map[$flag] = self::load($model, $where);
        }
        return $map[$flag];
    }

    public static function loadWithLock($model, $where)
    {
        if (self::isModel($model)) {
            $m = $model::where($where)->lockForUpdate()->first();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            $m = $m->where($where)->lockForUpdate()->first();
        }
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function exists($model, $where)
    {
        if (self::isModel($model)) {
            return $model::where($where)->exists();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            return $m->where($where)->exists();
        }
    }

    public static function generateHash($model, $field, $hashLength = 16)
    {
        if (self::isModel($model)) {
            do {
                $hash = strtolower(Str::random($hashLength));
            } while ($model::where([$field => $hash])->exists());
            return $hash;
        } else {
            do {
                $hash = strtolower(Str::random($hashLength));
                $m = new DynamicModel();
                $m->timestamps = self::$timestampEnable;
                $m->setTable($model);
            } while ($m->where([$field => $hash])->exists());
            return $hash;
        }
    }

    public static function fieldValues($model, $field, $where = [])
    {
        $fields = [];
        if (self::isModel($model)) {
            foreach ($model::where($where)->get()->toArray() as &$i) {
                $fields[] = $i[$field];
            }
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            foreach ($m->where($where)->get()->toArray() as &$i) {
                $fields[] = $i[$field];
            }
        }
        return $fields;
    }

    public static function find($model, $where = [], $order = null)
    {
        if (self::isModel($model)) {
            if ($order) {
                return $model::where($where)->orderBy($order[0], $order[1])->get()->toArray();
            }
            return $model::where($where)->get()->toArray();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            if ($order) {
                return $m->where($where)->orderBy($order[0], $order[1])->get()->toArray();
            }
            return $m->where($where)->get()->toArray();
        }
    }

    public static function map($model, $valueField = 'title', $keyField = 'id', $where = [], $order = null)
    {
        $items = self::find($model, $where, $order);
        $map = [];
        foreach ($items as $item) {
            $map[$item[$keyField]] = $item[$valueField];
        }
        return $map;
    }

    public static function first($model, $where = [], $order = null)
    {
        $record = null;
        if (self::isModel($model)) {
            if ($order) {
                $record = $model::where($where)->orderBy($order[0], $order[1])->first();
            } else {
                $record = $model::where($where)->first();
            }
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            if ($order) {
                $record = $m->where($where)->orderBy($order[0], $order[1])->first();
            } else {
                $record = $m->where($where)->first();
            }
        }
        if (empty($record)) {
            return null;
        }
        return $record->toArray();
    }

    public static function findFieldIn($model, $field, $in)
    {
        if (empty($in)) {
            return [];
        }
        $in = array_unique($in);
        if (self::isModel($model)) {
            return $model::whereIn($field, $in)->get()->toArray();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            return $m->whereIn($field, $in)->get()->toArray();
        }
    }

    public static function add($model, $data)
    {
        if (self::isModel($model)) {
            $m = new $model();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
        }
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        return $m->toArray();
    }

    public static function update($model, $where, $data)
    {
        if (empty($data)) {
            return null;
        }
        if (self::isModel($model)) {
            $m = $model::where($where)->get();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            $m = $m->where($where)->get();
        }

        if (empty($m)) {
            return null;
        }
        foreach ($m as $_m) {
            foreach ($data as $k => $v) {
                $_m->$k = $v;
            }
            $_m->save();
        }
        return $m->toArray();
    }

    public static function updateOne($model, $where, $data)
    {
        if (empty($data)) {
            return null;
        }
        if (self::isModel($model)) {
            $m = $model::where($where)->first();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            $m = $m->where($where)->first();
        }

        if (empty($m)) {
            return null;
        }
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        return $m->toArray();
    }

    public static function addOrUpdateOne($model, $where, $data)
    {
        if (self::isModel($model)) {
            $m = $model::where($where)->first();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            $m = $m->where($where)->first();
        }
        if (empty($m)) {

            // insert
            if (self::isModel($model)) {
                $m = new $model();
            } else {
                $m = new DynamicModel();
                $m->timestamps = self::$timestampEnable;
                $m->setTable($model);
            }
            foreach ($data as $k => $v) {
                $m->$k = $v;
            }
            $m->save();
            return $m->toArray();

        } else {

            // update
            foreach ($data as $k => $v) {
                if (array_key_exists($k, $where)) {
                    continue;
                }
                $m->$k = $v;
            }
            $m->save();
            return $m->toArray();

        }
    }

    public static function delete($model, $where)
    {
        if (self::isModel($model)) {
            $model::where($where)->delete();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            $m->where($where)->delete();
        }
    }

    public static function replaceConditionParamField(&$option, $fieldMap = [])
    {
        if (empty($fieldMap)) {
            return;
        }
        if (!empty($option['search']) && is_array($option['search'])) {
            foreach ($option['search'] as &$searchItem) {
                foreach ($searchItem as $field => $searchInfo) {
                    if (array_key_exists($field, $fieldMap)) {
                        unset($searchItem[$field]);
                        $searchItem[$fieldMap[$field]] = $searchInfo;
                    }
                }
            }
        }

        if (!empty($option['whereIn'])) {
            if (is_array($option['whereIn'][0])) {
                foreach ($option['whereIn'] as &$whereIn) {
                    if (array_key_exists($whereIn[0], $fieldMap)) {
                        $whereIn[0] = $fieldMap[$whereIn[0]];
                    }
                }
            } else {
                if (array_key_exists($option['whereIn'][0], $fieldMap)) {
                    $option['whereIn'][0] = $fieldMap[$option['whereIn'][0]];
                }
            }
        }

        if (!empty($option['whereOperate'])) {
            if (is_array($option['whereOperate'][0])) {
                foreach ($option['whereOperate'] as &$whereOperate) {
                    if (array_key_exists($whereOperate[0], $fieldMap)) {
                        $whereOperate[0] = $fieldMap[$whereOperate[0]];
                    }
                }
            } else {
                if (array_key_exists($option['whereOperate'][0], $fieldMap)) {
                    $option['whereOperate'][0] = $fieldMap[$option['whereOperate'][0]];
                }
            }
        }

        if (!empty($option['where'])) {
            foreach ($option['where'] as $k => $item) {
                if (array_key_exists($k, $fieldMap)) {
                    unset($option['where'][$k]);
                    $option['where'][$fieldMap[$k]] = $item;
                }
            }
        }
    }

    public static function mergeConditionParam(&$o, $option)
    {

        if (!empty($option['whereIn'])) {
            if (is_array($option['whereIn'][0])) {
                foreach ($option['whereIn'] as &$whereIn) {
                    $o = $o->whereIn($whereIn[0], $whereIn[1]);
                }
            } else {
                $o = $o->whereIn($option['whereIn'][0], $option['whereIn'][1]);
            }
        }

        if (!empty($option['whereOperate'])) {
            if (is_array($option['whereOperate'][0])) {
                foreach ($option['whereOperate'] as &$whereOperate) {
                    $o = $o->where($whereOperate[0], $whereOperate[1], $whereOperate[2]);
                }
            } else {
                $o = $o->where($option['whereOperate'][0], $option['whereOperate'][1], $option['whereOperate'][2]);
            }
        }

        if (!empty($option['where'])) {
            $o = $o->where($option['where']);
        }

        /**
         * $search = [];
         * $search[] = ['field1'=>['equal'=>value],'field2'=>['equal'=>value]];
         * $search[] = ['field1'=>['exp'=>'or','equal'=>value1,'like'=>'value2'],'field2'=>['equal'=>value]];
         * $search[] = ['__exp'=>'and|or','field1'=>[...],'field2'=>[...],];
         */
        if (!empty($option['search']) && is_array($option['search'])) {
            foreach ($option['search'] as $searchItem) {

                if (!isset($searchItem['__exp'])) {
                    $searchItem['__exp'] = 'and';
                } else {
                    $searchItem['__exp'] = strtolower($searchItem['__exp']);
                }

                $whereExpFirst = true;
                $whereExp = 'where';
                if ($searchItem['__exp'] == 'or') {
                    $whereExp = 'orWhere';
                }

                $o = $o->where(function ($queryBase) use (&$searchItem, $whereExpFirst, $whereExp) {

                    foreach ($searchItem as $field => $searchInfo) {
                        if (in_array($field, ['__exp'])) {
                            continue;
                        }
                        if (!isset($searchInfo['exp'])) {
                            $searchInfo['exp'] = 'and';
                        }
                        $searchInfo['exp'] = strtolower($searchInfo['exp']);

                        if ($whereExpFirst) {
                            $where = 'where';
                            $whereExpFirst = false;
                        } else {
                            $where = $whereExp;
                        }

                        $queryBase = $queryBase->$where(function ($query) use (&$field, &$searchInfo) {
                            $first = true;
                            foreach ($searchInfo as $k => $v) {
                                switch ($k) {
                                    case 'like':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, 'like', '%' . $v . '%');
                                        } else {
                                            $query->orWhere($field, 'like', '%' . $v . '%');
                                        }
                                        break;
                                    case 'leftLike':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, 'like', $v . '%');
                                        } else {
                                            $query->orWhere($field, 'like', $v . '%');
                                        }
                                        break;
                                    case 'rightLike':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, 'like', '%' . $v);
                                        } else {
                                            $query->orWhere($field, 'like', '%' . $v);
                                        }
                                        break;
                                    case 'equal':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '=', $v);
                                        } else {
                                            $query->orWhere($field, '=', $v);
                                        }
                                        break;
                                    case 'min':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '>=', $v);
                                        } else {
                                            $query->orWhere($field, '>=', $v);
                                        }
                                        break;
                                    case 'max':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '<=', $v);
                                        } else {
                                            $query->orWhere($field, '<=', $v);
                                        }
                                        break;
                                    case 'eq':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '=', $v);
                                        } else {
                                            $query->orWhere($field, '=', $v);
                                        }
                                        break;
                                    case 'in':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->whereIn($field, $v);
                                        } else {
                                            $query->whereIn($field, $v, 'or');
                                        }
                                        break;
                                    case 'is':
                                        if (null === $v) {
                                            if ($first || $searchInfo['exp'] == 'and') {
                                                $first = false;
                                                $query->whereNull($field);
                                            } else {
                                                $query->orWhereNull($field);
                                            }
                                        } else {
                                            exit('TODO');
                                        }
                                        break;
                                }
                            }
                        });

                    }

                });

//                foreach ($searchItem as $field => $searchInfo) {
//                    if (isset($searchInfo['equal'])) {
//
//                        $o = $o->where([$field => $searchInfo['equal']]);
//
//                    } else if (isset($searchInfo['exp']) && strtolower($searchInfo['exp']) == 'or') {
//
//                        $o = $o->where(function ($query) use (&$field, &$searchInfo) {
//                            $first = true;
//                            foreach ($searchInfo as $k => $v) {
//                                switch ($k) {
//                                    case 'like':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->where($field, 'like', '%' . $v . '%');
//                                        } else {
//                                            $query->orWhere($field, 'like', '%' . $v . '%');
//                                        }
//                                        break;
//                                    case 'leftLike':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->where($field, 'like', $v . '%');
//                                        } else {
//                                            $query->orWhere($field, 'like', $v . '%');
//                                        }
//                                        break;
//                                    case 'rightLike':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->where($field, 'like', '%' . $v);
//                                        } else {
//                                            $query->orWhere($field, 'like', '%' . $v);
//                                        }
//                                        break;
//                                    case 'min':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->where($field, '>=', $v);
//                                        } else {
//                                            $query->orWhere($field, '>=', $v);
//                                        }
//                                        break;
//                                    case 'max':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->where($field, '<=', $v);
//                                        } else {
//                                            $query->orWhere($field, '<=', $v);
//                                        }
//                                        break;
//                                    case 'eq':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->where($field, '=', $v);
//                                        } else {
//                                            $query->orWhere($field, '=', $v);
//                                        }
//                                        break;
//                                    case 'in':
//                                        if ($first) {
//                                            $first = false;
//                                            $query->whereIn($field, $v);
//                                        } else {
//                                            $query->whereIn($field, $v, 'or');
//                                        }
//                                        break;
//                                    case 'is':
//                                        if (null === $v) {
//                                            if ($first) {
//                                                $first = false;
//                                                $query->whereNull($field, 'is', $v);
//                                            } else {
//                                                $query->osWhereNull($field, 'is', $v);
//                                            }
//                                        } else {
//                                            exit('TODO');
//                                        }
//                                        break;
//                                }
//                            }
//                        });
//
//                    } else {
//
//                        foreach ($searchInfo as $k => $v) {
//                            switch ($k) {
//                                case 'like':
//                                    $o = $o->where($field, 'like', '%' . $v . '%');
//                                    break;
//                                case 'leftLike':
//                                    $o = $o->where($field, 'like', $v . '%');
//                                    break;
//                                case 'rightLike':
//                                    $o = $o->where($field, 'like', '%' . $v);
//                                    break;
//                                case 'min':
//                                    $o = $o->where($field, '>=', $v);
//                                    break;
//                                case 'max':
//                                    $o = $o->where($field, '<=', $v);
//                                    break;
//                                case 'in':
//                                    $o = $o->whereIn($field, $v);
//                                    break;
//                                case 'is':
//                                    if (null === $v) {
//                                        $o = $o->whereNull($field);
//                                    } else {
//                                        exit('TODO');
//                                    }
//                                    break;
//                            }
//                        }
//
//                    }
//                }
            }
        }
    }

    public static function modelPaginate($model, $page, $pageSize, $option = [])
    {
        if (self::isModel($model)) {
            $m = $model::where([]);
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
        }

        if (!empty($option['joins'])) {
            $select = [];
            $select[] = $model . '.*';
            foreach ($option['joins'] as $join) {
                if (!empty($join['table']) && !empty($join['fields'])) {
                    $m = $m->leftJoin($join['table'][0], $join['table'][1], $join['table'][2], $join['table'][3]);
                    foreach ($join['fields'] as $fieldAlias => $fieldTable) {
                        array_push($select, "$fieldTable as $fieldAlias");
                    }
                }
            }
            $m = call_user_func_array(array($m, 'select'), $select);
        }

        self::mergeConditionParam($m, $option);

        if (!empty($option['order'])) {
            if (is_array($option['order'][0])) {
                foreach ($option['order'] as &$order) {
                    $m = $m->orderBy($order[0], $order[1]);
                }
            } else {
                $m = $m->orderBy($option['order'][0], $option['order'][1]);
            }
        }

        if (!empty($option['fields'])) {
            $m = $m->select($option['fields']);
        }

        $m = $m->paginate($pageSize, ['*'], 'page', $page)->toArray();

        return [
            'total' => $m['total'],
            'records' => $m['data']
        ];
    }

    /**
     * @param $model
     * @return Builder | Model
     */
    public static function model($model)
    {
        if (self::isModel($model)) {
            return new $model();
        }
        $m = new DynamicModel();
        $m->timestamps = self::$timestampEnable;
        $m->setTable($model);
        return $m;
    }

    public static function change($model, $where, $field, $value)
    {
        if (self::isModel($model)) {
            $m = $model::where([]);
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
        }
        if ($value > 0) {
            $m->where($where)->increment($field, $value);
        } else {
            $m->where($where)->decrement($field, -$value);
        }
    }


    public static function count($model, $where = [])
    {
        if (self::isModel($model)) {
            return $model::where($where)->count();
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            return $m->where($where)->count();
        }
    }


    public static function sum($model, $field, $where = [])
    {
        if (self::isModel($model)) {
            return $model::where($where)->sum($field);
        } else {
            $m = new DynamicModel();
            $m->timestamps = self::$timestampEnable;
            $m->setTable($model);
            return $m->where($where)->sum($field);
        }
    }

    public static function transactionBegin()
    {
        DB::beginTransaction();
    }

    public static function transactionRollback()
    {
        DB::rollback();
    }

    public static function transactionCommit()
    {
        DB::commit();
    }

    public static function truncate($model)
    {
        DB::table($model)->truncate();
    }

    public static function decodeRecordJson(&$record, $keyArray, $default = [])
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            $record[$key] = @json_decode($record[$key], true);
            if (empty($record[$key])) {
                $record[$key] = $default;
            }
        }
    }

    public static function encodeRecordJson(&$record, $keyArray, $default = [])
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            if (empty($record[$key])) {
                $record[$key] = $default;
            }
            $record[$key] = @json_encode($record[$key]);
        }
    }

    public static function decodeRecordsJson(&$records, $keyArray, $default = [])
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = @json_decode($record[$key], true);
                if (empty($record[$key])) {
                    $record[$key] = $default;
                }
            }
        }
    }

    public static function encodeRecordsJson(&$records, $keyArray, $default = [])
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                if (empty($record[$key])) {
                    $record[$key] = $default;
                }
                $record[$key] = @json_encode($record[$key]);
            }
        }
    }
}