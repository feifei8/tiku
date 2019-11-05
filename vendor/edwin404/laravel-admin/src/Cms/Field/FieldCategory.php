<?php

namespace Edwin404\Admin\Cms\Field;

use Edwin404\Admin\Cms\Helper\CategoryCmsHelper;
use Edwin404\Base\Support\TreeHelper;
use Illuminate\Support\Facades\View;

class FieldCategory extends BaseField
{
    public $model;
    public $modelId = 'id';
    public $modelPid = 'pid';
    public $modelSort = 'sort';
    public $modelTitle = 'title';

    public function viewHtml(&$data)
    {
        return $this->listHtml($data);
    }


    public function listHtml(&$data)
    {
        $parents = CategoryCmsHelper::loadCategoryWithParents($this->model, $data, $this->modelId, $this->modelPid);
        if (empty($parents)) {
            return '无';
        }
        $cats = [];
        foreach ($parents as &$parent) {
            $cats[] = $parent[$this->modelTitle];
        }
        return join(' &gt; ', $cats);
    }

    public function addHtml()
    {
        $options = TreeHelper::model2Nodes($this->model, ['title' => $this->modelTitle]);
        $options = TreeHelper::listIndent($options, 'id', 'title');

        return View::make('admin::cms.field.category.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$options,
            'default' => $this->default,
        ])->render();
    }


    public function editHtml(&$data)
    {
        $options = TreeHelper::model2Nodes($this->model, ['title' => $this->modelTitle]);
        $options = TreeHelper::listIndent($options, 'id', 'title');

        return View::make('admin::cms.field.category.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$options,
            'data' => &$data
        ])->render();
    }

}