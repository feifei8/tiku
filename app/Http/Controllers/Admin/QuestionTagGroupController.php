<?php
namespace App\Http\Controllers\Admin;

use Edwin404\Admin\Cms\Field\FieldEmpty;
use Edwin404\Admin\Cms\Field\FieldImage;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Field\FieldTextarea;
use Edwin404\Admin\Cms\Handle\CategoryCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class QuestionTagGroupController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'question_tag_group',
        'pageTitle' => '题目标签组',
        'group' => 'data',
        'maxLevel' => 1,
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'canSort' => true,
        'fields' => [
            'title' => ['type' => FieldText::class, 'title' => '名称', 'list' => true, 'add' => true, 'edit' => true, 'view' => true],
        ]
    ];

    public function dataList(CategoryCms $categoryCms)
    {
        return $categoryCms->executeList($this, $this->cmsConfigData);
    }

    public function dataAdd(CategoryCms $categoryCms)
    {
        return $categoryCms->executeAdd($this, $this->cmsConfigData);
    }

    public function dataEdit(CategoryCms $categoryCms)
    {
        return $categoryCms->executeEdit($this, $this->cmsConfigData);
    }

    public function dataDelete(CategoryCms $categoryCms)
    {
        return $categoryCms->executeDelete($this, $this->cmsConfigData);
    }

    public function dataView(CategoryCms $categoryCms)
    {
        return $categoryCms->executeView($this, $this->cmsConfigData);
    }

    public function dataSort(CategoryCms $categoryCms)
    {
        return $categoryCms->executeSort($this, $this->cmsConfigData);
    }

}