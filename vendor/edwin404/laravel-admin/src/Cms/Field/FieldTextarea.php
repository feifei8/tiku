<?php

namespace Edwin404\Admin\Cms\Field;

use Edwin404\Base\Support\HtmlHelper;
use Illuminate\Support\Facades\View;

class FieldTextarea extends BaseField
{
    public function addHtml()
    {
        return View::make('admin::cms.field.textarea.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.textarea.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function viewHtml(&$data)
    {
        return HtmlHelper::text2html($data);
    }
}