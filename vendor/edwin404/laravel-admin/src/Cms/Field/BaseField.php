<?php

namespace Edwin404\Admin\Cms\Field;


use Illuminate\Support\Facades\View;

abstract class BaseField
{
    protected $context = null;

    public $key = '';
    public $title = '';
    public $desc = '';
    public $default = null;

    public $add = false;
    public $edit = false;
    public $delete = false;
    public $list = false;
    public $view = false;
    public $search = false;
    public $export = false;

    // 原始field配置数组
    public $field;

    public $listLimit = 40;

    public function __construct(&$context)
    {
        $this->context = $context;
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.base.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.base.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function searchHtml()
    {
        return View::make('admin::cms.field.base.search', [
            'key' => &$this->key,
            'field' => &$this->field,
        ])->render();
    }

    public function viewHtml(&$data)
    {
        return htmlspecialchars($data);
    }

    public function listHtml(&$data)
    {
        if (mb_strlen($data) > $this->listLimit) {
            return htmlspecialchars(mb_strcut($data, 0, $this->listLimit)) . '...';
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8', false);
    }

    public function exportValue(&$data)
    {
        return $data;
    }

    public function inputGet($inputAll)
    {
        if (isset($inputAll[$this->key])) {
            return $inputAll[$this->key];
        }
        return null;
    }

    // 如果出错,返回标准错误
    public function inputProcess($value)
    {
        return ['code' => 0, 'msg' => null, 'data' => $value];
    }

    public function valueSerialize($value)
    {
        return $value;
    }

    public function valueUnserialize($value)
    {
        return $value;
    }
}