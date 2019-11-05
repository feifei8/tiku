<?php
namespace App\Http\Controllers\Admin;

use App\Types\EventShowStatus;
use App\Types\EventStatus;
use Edwin404\Admin\Cms\Field\FieldAttr;
use Edwin404\Admin\Cms\Field\FieldCategory;
use Edwin404\Admin\Cms\Field\FieldDate;
use Edwin404\Admin\Cms\Field\FieldDatetime;
use Edwin404\Admin\Cms\Field\FieldFields;
use Edwin404\Admin\Cms\Field\FieldImage;
use Edwin404\Admin\Cms\Field\FieldImages;
use Edwin404\Admin\Cms\Field\FieldRichtext;
use Edwin404\Admin\Cms\Field\FieldSelect;
use Edwin404\Admin\Cms\Field\FieldSwitch;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Field\FieldTextarea;
use Edwin404\Admin\Cms\Handle\BasicCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;

class QuestionTagController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'question_tag',
        'pageTitle' => '题目标签',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'canSort' => true,
        'fields' => [
            'groupId' => ['type' => FieldCategory::class, 'title' => '标签组', 'list' => true, 'add' => true, 'edit' => true, 'view' => true, 'model' => 'question_tag_group'],
            'title' => ['type' => FieldText::class, 'title' => '名称', 'list' => true, 'add' => true, 'edit' => true, 'view' => true],
            'cover' => ['type' => FieldImage::class, 'title' => '图标', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'description' => ['type' => FieldTextarea::class, 'title' => '说明', 'add' => true, 'edit' => true, 'view' => true,],
        ]
    ];

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    public function dataAdd(BasicCms $basicCms)
    {
        return $basicCms->executeAdd($this, $this->cmsConfigData);
    }

    public function dataEdit(BasicCms $basicCms)
    {
        return $basicCms->executeEdit($this, $this->cmsConfigData);
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigData);
    }

    public function dataView(BasicCms $basicCms)
    {
        return $basicCms->executeView($this, $this->cmsConfigData);
    }

}