<?php

namespace Edwin404\Tecmz\Controllers;


use Edwin404\Admin\Services\AdminUserService;
use Edwin404\Base\Support\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class InstallController extends Controller
{
    public function lock()
    {
        file_put_contents('../storage/install.lock', 'lock');
        return Response::send(0, 'install lock ok ^_^');
    }

    public function ping()
    {
        return 'ok';
    }

    public function execute(AdminUserService $adminUserService)
    {
        if (file_exists('../storage/install.lock')) {
            echo "删除 install.lock 文件再安装 T_T";
            return;
        }

        $username = Input::get("username");
        $password = Input::get("password");
        if (empty($username)) {
            echo "管理用户名为空";
            return;
        }
        if (empty($password)) {
            echo "管理用户密码为空";
            return;
        }

        $exitCode = Artisan::call("migrate");
        if (0 != $exitCode) {
            echo "安装错误 exitCode($exitCode)";
            return;
        }

        $adminUserService->add($username, $password);

        // 初始化数据
        if (file_exists('./data_demo/data.php')) {
            $data = include('./data_demo/data.php');
            foreach ($data as $table => $records) {
                DB::table($table)->insert($records);
            }
        }

        echo 'ok';
    }
}