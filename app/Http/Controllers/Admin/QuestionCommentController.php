<?php

namespace App\Http\Controllers\Admin;

use App\Types\EventShowStatus;
use App\Types\EventStatus;
use Edwin404\Admin\Cms\Field\FieldAttr;
use Edwin404\Admin\Cms\Field\FieldCategory;
use Edwin404\Admin\Cms\Field\FieldDate;
use Edwin404\Admin\Cms\Field\FieldDatetime;
use Edwin404\Admin\Cms\Field\FieldEmpty;
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
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Tecmz\Helpers\AdminCmsHelper;

class QuestionCommentController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'question_comment',
        'pageTitle' => '评论管理',
        'group' => 'data',
//        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
//        'canView' => true,
        'fields' => [
            'questionId' => ['type' => FieldText::class, 'title' => '题目', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'memberUserId' => ['type' => FieldText::class, 'title' => '用户', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
            'content' => ['type' => FieldRichtext::class, 'title' => '内容', 'list' => true, 'add' => true, 'edit' => true, 'view' => true,],
        ]

    ];

    public function dataProcessView(&$item, &$record)
    {
        $item['memberUserId'] = AdminCmsHelper::memberUserId($record['memberUserId']);
        $question = ModelHelper::loadWithCache('question', ['id' => $item['questionId']]);
        if (!empty($question)) {
            $item['questionId'] = '<a target="_blank" href="' . action('\App\Http\Controllers\Main\QuestionController@view', ['alias' => $question['alias']]) . '">' . $question['question'] . '</a>';
        }
    }

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