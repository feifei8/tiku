<?php

namespace Edwin404\Admin\Cms\Field;

use Edwin404\Base\Support\FileHelper;
use Edwin404\Base\Support\PathHelper;
use Illuminate\Support\Facades\View;

class FieldFile extends BaseField
{
    public $server = null;
    public $cdn = '';

    public function __construct(&$context)
    {
        parent::__construct($context);
    }


    public function viewHtml(&$data)
    {
        if ($data) {
            return '<a href="' . PathHelper::fix($data, $this->cdn) . '" target="_blank" data-uk-tooltip title="' . FileHelper::name($data) . '">' . strtoupper(FileHelper::extension($data)) . '文件</a>';
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            return '<a href="' . PathHelper::fix($data, $this->cdn) . '" target="_blank" data-uk-tooltip title="' . FileHelper::name($data) . '">' . strtoupper(FileHelper::extension($data)) . '文件</a>';
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.file.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.file.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'data' => &$data,
        ])->render();
    }

}