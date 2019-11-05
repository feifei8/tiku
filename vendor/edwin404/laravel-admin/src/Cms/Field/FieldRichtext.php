<?php

namespace Edwin404\Admin\Cms\Field;

use Edwin404\Base\Support\HtmlHelper;
use Edwin404\Data\Facades\DataFacade;
use Illuminate\Support\Facades\View;

class FieldRichtext extends BaseField
{
    public function addHtml()
    {
        return View::make('admin::cms.field.richtext.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.richtext.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function viewHtml(&$data)
    {
        return $data;
    }

    public function listHtml(&$data)
    {
        $summary = HtmlHelper::extractTextAndImages($data);
        return parent::listHtml($summary['text']);
    }

    public function inputProcess($value)
    {
        $value = DataFacade::storeContentTempPath($value);
        return ['code' => 0, 'msg' => null, 'data' => $value];
    }


}