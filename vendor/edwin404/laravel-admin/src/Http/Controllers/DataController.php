<?php

namespace Edwin404\Admin\Http\Controllers;

use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Data\Support\AudioSelectDialogHandle;
use Edwin404\Data\Support\DataUploadHandle;
use Edwin404\Data\Support\ImageSelectDialogHandle;
use Edwin404\Data\Support\PutDataDialogHandle;
use Edwin404\Data\Support\SelectDialogHandle;
use Edwin404\Data\Support\UEditorHandle;

class DataController extends AdminCheckController
{
    public function imageSelectDialog(ImageSelectDialogHandle $imageSelectDialogHandle)
    {
        // 弃用这个方法
        return $imageSelectDialogHandle->execute();
    }

    public function selectDialog(SelectDialogHandle $selectDialogHandle, $category)
    {
        $option = [];
        $option['driver'] = ConfigFacade::get('uploadDriver', '');
        $option['domain'] = ConfigFacade::get('uploadDriverDomain', '');
        switch ($option['driver']) {
            case 'ossAliyun':
                $option['aliyunAccessKeyId'] = ConfigFacade::get('uploadDriverAliyunAccessKeyId', '');
                $option['aliyunAccessKeySecret'] = ConfigFacade::get('uploadDriverAliyunAccessKeySecret', '');
                $option['aliyunEndpoint'] = ConfigFacade::get('uploadDriverAliyunEndpoint', '');
                $option['aliyunBucket'] = ConfigFacade::get('uploadDriverAliyunBucket', '');
                break;
        }
        return $selectDialogHandle->execute($category, $option);
    }

    public function putDataDialog(PutDataDialogHandle $putDataDialogHandle, $category)
    {
        return $putDataDialogHandle->execute($category);
    }

    public function tempDataUpload(DataUploadHandle $dataUploadHandle, $category = '')
    {
        $option = [];
        $option['driver'] = ConfigFacade::get('uploadDriver', '');
        $option['domain'] = ConfigFacade::get('uploadDriverDomain', '');
        switch ($option['driver']) {
            case 'ossAliyun':
                $option['aliyunAccessKeyId'] = ConfigFacade::get('uploadDriverAliyunAccessKeyId', '');
                $option['aliyunAccessKeySecret'] = ConfigFacade::get('uploadDriverAliyunAccessKeySecret', '');
                $option['aliyunEndpoint'] = ConfigFacade::get('uploadDriverAliyunEndpoint', '');
                $option['aliyunBucket'] = ConfigFacade::get('uploadDriverAliyunBucket', '');
                break;
        }
        return $dataUploadHandle->temp($category, $option);
    }

    public function ueditorHandle(UEditorHandle $UEditorHandle)
    {
        return $UEditorHandle->executeForAdmin();
    }
}
