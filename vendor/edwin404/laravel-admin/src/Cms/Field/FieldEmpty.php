<?php

namespace Edwin404\Admin\Cms\Field;

class FieldEmpty extends BaseField
{
    public function addHtml()
    {
        return null;
    }

    public function editHtml(&$data)
    {
        return null;
    }

    public function viewHtml(&$data)
    {
        return $data;
    }

    public function listHtml(&$data)
    {
        return $data;
    }

}