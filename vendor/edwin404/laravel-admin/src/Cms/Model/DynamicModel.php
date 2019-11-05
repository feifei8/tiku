<?php

namespace Edwin404\Admin\Cms\Model;

use Illuminate\Database\Eloquent\Model;

class DynamicModel extends Model
{
    protected static $_table;

    public function setTable($table)
    {
        static::$_table = $table;
    }

    public function getTable()
    {
        return static::$_table;
    }
}