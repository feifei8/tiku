<?php

namespace Edwin404\Tecmz\Controllers;

use Edwin404\Admin\Cms\Field\FieldImage;
use Edwin404\Admin\Cms\Field\FieldSelect;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Handle\BasicCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Banner\Services\BannerService;
use Edwin404\Base\Support\Response;
use Illuminate\Support\Facades\Request;

class BannerController extends AdminCheckController
{
    protected $cmsConfigBasic = [
        'model' => 'banner',
        'pageTitle' => '轮播',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'fields' => [
            'position' => ['type' => FieldSelect::class, 'title' => '位置', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '请保持默认', 'options' => [
                'pcHome' => 'PC首页',
                'mHome' => '手机首页',
            ]],
            'sort' => ['type' => FieldText::class, 'title' => '排序', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '数字越小越靠前',],
            'image' => ['type' => FieldImage::class, 'title' => '图片', 'list' => true, 'edit' => true, 'add' => true,],
            'link' => ['type' => FieldText::class, 'title' => '链接', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '链接为空将不会跳转',],
        ]
    ];

    private $bannerService;

    public function __construct(BannerService $bannerService)
    {
        parent::__construct();
        $this->bannerService = $bannerService;
        $this->setUpConfig();
    }

    protected function setUpConfig()
    {

    }

    public function dataPostAdd(&$data)
    {
        $this->bannerService->clearCache($data['position']);
    }

    public function dataPostEdit(&$data)
    {
        $this->bannerService->clearCache($data['position']);
    }

    public function dataPostDelete(&$data)
    {
        $this->bannerService->clearCache($data['position']);
    }

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigBasic);;
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigBasic);
    }

    public function dataEdit(BasicCms $basicCms)
    {
        return $basicCms->executeEdit($this, $this->cmsConfigBasic);
    }

    public function dataAdd(BasicCms $basicCms)
    {
        return $basicCms->executeAdd($this, $this->cmsConfigBasic);
    }


}