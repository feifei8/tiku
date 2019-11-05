<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use Edwin404\Data\Support\DataUploadTrait;
use Edwin404\Data\Support\ImageSelectDialogHandle;
use Edwin404\Member\Support\MemberLoginCheck;

class DataController extends BaseController implements MemberLoginCheck
{
    use DataUploadTrait;

    public function imageSelectDialog(ImageSelectDialogHandle $imageSelectDialogHandle)
    {
        return $imageSelectDialogHandle->executeForMemberUser($this->memberUserId());
    }

    public function tempUpload($category)
    {
        return $this->tempDataUpload($category);
    }
}