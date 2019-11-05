<?php
namespace Edwin404\Admin\Http\Controllers;

use App\Constant\AppConstant;
use Chumper\Zipper\Zipper;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\CurlHelper;
use Edwin404\Base\Support\FileHelper;
use Edwin404\Base\Support\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class UpgradeController extends AdminCheckController
{
    public function index($action)
    {
        if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
            return Response::send(-1, '演示账号禁止修改信息');
        }

        if ($this->adminUserId() != env('ADMIN_FOUNDER_ID', 1)) {
            return Response::json(-1, '您无权操作，只有创建者才能进行系统');
        }

        // 初次访问请求 /upgrade/init
        // 返回success("msg")会提示，程序会自动请求当前 /system/upgrade
        // 返回success("ok")表示升级完成，程序会自动注销当前登录
        switch ($action) {
            case 'init':
                $domain = Request::server('HTTP_HOST');
                $url = AppConstant::UPGRADE_URL . '?version=' . AppConstant::VERSION . '&domain=' . $domain;
                $json = CurlHelper::getStandardJson($url);
                if ($json['code']) {
                    return Response::json(-1, $json['msg']);
                }
                Session::put('upgradePackage', $json['data']['upgradePackage']);
                return Response::json(0, 'ok', ['action' => 'download', 'msg' => '获取升级包信息成功，下载升级包...']);
            case 'download':
                @set_time_limit(0);
                $upgradePackage = Session::get('upgradePackage', null);
                if (!$upgradePackage) {
                    return Response::json(-1, '升级包获取失败');
                }
                $filename = substr($upgradePackage, strrpos($upgradePackage, '/') + 1);
                $response = CurlHelper::getHeaderAndContent($upgradePackage);
                if (empty($response)) {
                    return Response::json(-1, '获取升级包失败');
                }
                $isZip = false;
                foreach ($response['header'] as $header) {
                    foreach ($header as $k => $v) {
                        if ($k == 'content-type') {
                            if (preg_match('/application\\/zip/', $v)) {
                                $isZip = true;
                            }
                            break;
                        }
                    }
                }
                if (!$isZip) {
                    return Response::json(-1, '获取升级包zip文件失败');
                }
                file_put_contents($filename, $response['body']);
                Session::put('upgradePackage', $filename);
                return Response::json(0, 'ok', ['action' => 'unpack', 'msg' => '下载升级包成功，准备解压...']);
            case 'unpack':
                $filename = Session::get('upgradePackage');
                $dir = substr($filename, 0, strrpos($filename, '.'));
                $zipper = new Zipper();
                if (!file_exists($dir)) {
                    @mkdir($dir);
                }
                $zipper->make($filename)->extractTo($dir, []);
                return Response::json(0, 'ok', ['action' => 'copy', 'msg' => '解压文件成功，准备复制文件...']);
            case 'copy':
                $filename = Session::get('upgradePackage');
                $dir = substr($filename, 0, strrpos($filename, '.'));
                FileHelper::copy($dir . '/', '../', '.mzback.' . date('YmdHis', time()));
                Artisan::call("migrate");
                Artisan::call("cache:clear");
                Artisan::call("view:clear");
                return Response::json(0, 'ok', ['action' => 'clean', 'msg' => '复制文件成功，准备清理文件...']);
            case 'clean':
                $filename = Session::get('upgradePackage');
                $dir = substr($filename, 0, strrpos($filename, '.'));
                FileHelper::rm($dir . '/', true);
                FileHelper::rm($filename, true);
                Session::forget('upgradePackage');
                return Response::json(0, 'ok', ['action' => 'finish']);
        }
        return Response::json(-1, '升级出现未知错误');
    }
}