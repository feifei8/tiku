<?php

namespace Edwin404\Admin\Http\Controllers;


use Edwin404\Admin\Facades\AdminUserFacade;
use Edwin404\Admin\Helpers\AdminLogHelper;
use Edwin404\Admin\Helpers\AdminPowerHelper;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Admin\Models\AdminRole;
use Edwin404\Admin\Models\AdminRoleRule;
use Edwin404\Admin\Models\AdminUser;
use Edwin404\Admin\Models\AdminUserRole;
use Edwin404\Admin\Services\AdminUserService;
use Edwin404\Admin\Type\AdminLogType;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Demo\Helpers\DemoHelper;
use EdwinFound\Utils\ArrayUtil;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class SystemController extends AdminCheckController
{
    public function clearCache()
    {
        if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
            return Response::send(-1, '演示账号禁止修改信息');
        }

        AdminLogHelper::addInfoLog('清除缓存');

        $exitCode = Artisan::call("cache:clear");
        if (0 != $exitCode) {
            return Response::send(-1, "清除缓存失败 cache exitCode($exitCode)");
        }

        $exitCode = Artisan::call("view:clear");
        if (0 != $exitCode) {
            return Response::send(-1, "清除缓存失败 view exitCode($exitCode)");
        }

        return Response::send(0, '操作成功');
    }

    public function changePwd(AdminUserService $adminUserService)
    {
        if (Request::isMethod('post')) {

            if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
                return Response::send(-1, '演示账号禁止修改信息');
            }

            $passwordOld = trim(Input::get('passwordOld'));
            $passwordNew = trim(Input::get('passwordNew'));
            $passwordNewRepeat = trim(Input::get('passwordNewRepeat'));

            if ($passwordNew != $passwordNewRepeat) {
                return Response::send(-1, '两次新密码不一致');
            }

            $ret = $adminUserService->changePwd($this->adminUserId(), $passwordOld, $passwordNew);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }

            AdminLogHelper::addInfoLog('修改密码');

            return Response::send(0, '密码修改成功', null, '[reload]');
        }

        return view('admin::system.changepwd');
    }

    public function userRoleList()
    {
        if (Request::isMethod('post')) {

            $page = Input::get('page');
            $pageSize = 20;
            $option = [];

            $option['order'] = ['id', 'desc'];

            $head = [];
            $head[] = ['field' => 'name', 'title' => '角色',];
            $head[] = ['field' => 'users', 'title' => '用户',];
            $head[] = ['field' => '_operation', 'title' => '操作'];

            $paginateData = ModelHelper::modelPaginate(AdminRole::class, $page, $pageSize, $option);

            $list = [];
            foreach ($paginateData['records'] as $record) {

                $userRoles = ModelHelper::find(AdminUserRole::class, ['roleId' => $record['id']]);
                $users = [];
                foreach ($userRoles as $userRole) {
                    $user = ModelHelper::load(AdminUser::class, ['id' => $userRole['userId']]);
                    if (empty($user)) {
                        continue;
                    }
                    $users[] = $user['username'];
                }

                $operation = [];
                if (AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@userRoleEdit')) {
                    $operation[] = '<a href="#" data-dialog-request="' . action('\Edwin404\Admin\Http\Controllers\SystemController@userRoleEdit', ['id' => $record['id']]) . '">编辑</a>';
                }
                if (AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@userRoleDelete')) {
                    $operation[] = '<a href="#" data-confirm="确认删除" data-ajax-request="' . action('\Edwin404\Admin\Http\Controllers\SystemController@userRoleDelete', ['id' => $record['id']]) . '">删除</a>';
                }

                $item = [
                    '_id' => $record['id'],
                    'name' => $record['name'],
                    'users' => join(',', $users),
                    '_operation' => join(" - ", $operation)
                ];
                $list[] = $item;
            }

            $data = [];
            $data['head'] = $head;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::json(0, null, $data);
        }

        return view('admin::system.role.list');
    }

    public function userRoleEdit($id = 0)
    {
        $role = ModelHelper::load(AdminRole::class, ['id' => $id]);

        if (Request::isMethod('post')) {

            if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
                return Response::send(-1, '演示账号禁止修改信息');
            }

            $data = [];
            $data['name'] = trim(Input::get('name'));

            if (empty($data['name'])) {
                return Response::send(-1, '角色名称不能为空');
            }

            $rules = Input::get('rules');
            if (!is_array($rules)) {
                $rules = [];
            }
            if (empty($rules)) {
                return Response::send(-1, '角色权限不能为空');
            }

            if ($role) {
                $adminRole = ModelHelper::load('admin_role', ['id' => $role['id']]);
                AdminLogHelper::addInfoLogIfChanged('修改管理员角色', [
                    '名称' => $adminRole['name'],
                ], [
                    '名称' => $data['name']
                ]);
                $adminRoleRules = ModelHelper::find('admin_role_rule', ['roleId' => $adminRole['id']]);
                $adminRoleRules = ArrayUtil::fetchSpecifiedKeyToArray($adminRoleRules, 'rule');
                if (!ArrayUtil::sequenceEqual($adminRoleRules, $rules)) {
                    AdminLogHelper::addInfoLog('修改管理员角色权限');
                }
                $adminRole = ModelHelper::updateOne(AdminRole::class, ['id' => $role['id']], $data);
            } else {
                $adminRole = ModelHelper::add(AdminRole::class, $data);
                AdminLogHelper::addInfoLog('增加管理员角色', [
                    '名称' => $data['name']
                ]);
            }
            ModelHelper::delete(AdminRoleRule::class, ['roleId' => $adminRole['id']]);
            foreach ($rules as $rule) {
                ModelHelper::add(AdminRoleRule::class, ['roleId' => $adminRole['id'], 'rule' => $rule]);
            }

            ModelHelper::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

            return Response::send(0, null, null, '[js]$.dialogClose()');
        }

        $rules = [];
        if ($role) {
            $roleRules = ModelHelper::find(AdminRoleRule::class, ['roleId' => $role['id']]);
            foreach ($roleRules as $roleRule) {
                $rules[$roleRule['rule']] = true;
            }
        }

        $viewData = [];
        $viewData['role'] = $role;
        $viewData['rules'] = $rules;
        $viewData['powers'] = AdminPowerHelper::rules('powerList');
        return view('admin::system.role.edit', $viewData);
    }

    public function userRoleDelete($id = 0)
    {
        if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
            return Response::send(-1, '演示账号禁止修改信息');
        }

        $adminUserRole = ModelHelper::load('admin_role', ['id' => $id]);
        if (empty($adminUserRole)) {
            return Response::send(-1, '记录不存在');
        }

        ModelHelper::delete(AdminUserRole::class, ['roleId' => $id]);
        ModelHelper::delete(AdminRoleRule::class, ['roleId' => $id]);
        ModelHelper::delete(AdminRole::class, ['id' => $id]);

        ModelHelper::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

        AdminLogHelper::addInfoLog('删除管理员角色', [
            '名称' => $adminUserRole['name'],
        ]);

        return Response::send(0, null, null, '[js]window.lister.load(false);');
    }

    public function userList(AdminUserService $adminUserService)
    {
        if (Request::isMethod('post')) {

            $page = Input::get('page');
            $pageSize = 20;
            $option = [];

            $option['order'] = ['id', 'desc'];

            $head = [];
            $head[] = ['field' => 'username', 'title' => '用户',];
            $head[] = ['field' => 'roles', 'title' => '角色',];
            $head[] = ['field' => '_operation', 'title' => '操作'];

            $paginateData = ModelHelper::modelPaginate(AdminUser::class, $page, $pageSize, $option);

            $list = [];
            foreach ($paginateData['records'] as $record) {

                $adminRoles = $adminUserService->getRolesByUserId($record['id']);
                $roles = [];
                foreach ($adminRoles['data'] as $adminRole) {
                    $roles[] = $adminRole['name'];
                }

                $operation = [];

                if (AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@userEdit')) {
                    $operation[] = '<a href="#" data-dialog-request="' . action('\Edwin404\Admin\Http\Controllers\SystemController@userEdit', ['id' => $record['id']]) . '">编辑</a>';
                }

                if (AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@userDelete')) {
                    $operation[] = '<a href="#" data-confirm="确认删除" data-ajax-request="' . action('\Edwin404\Admin\Http\Controllers\SystemController@userDelete', ['id' => $record['id']]) . '">删除</a>';
                }

                if ($record['id'] == env('ADMIN_FOUNDER_ID', 1)) {
                    $operation = [];
                    $roles = ['创建者'];
                }

                $item = [
                    '_id' => $record['id'],
                    'username' => $record['username'],
                    'roles' => join('，', $roles),
                    '_operation' => join(' - ', $operation),
                ];
                $list[] = $item;
            }

            $data = [];
            $data['head'] = $head;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::json(0, null, $data);
        }
        return view('admin::system.user.list');
    }

    public function userEdit(AdminUserService $adminUserService, $id = 0)
    {

        if ($id == env('ADMIN_FOUNDER_ID', 1)) {
            return Response::send(-1, '创建者不能修改');
        }

        $adminUser = ModelHelper::load(AdminUser::class, ['id' => $id]);
        $roles = ModelHelper::find(AdminRole::class, []);
        $adminUserRoleIds = [];
        if ($adminUser) {
            $adminUserRoles = ModelHelper::find(AdminUserRole::class, ['userId' => $adminUser['id']]);
            foreach ($adminUserRoles as $adminUserRole) {
                $adminUserRoleIds[] = $adminUserRole['roleId'];
            }
        }

        if (Request::isMethod('post')) {

            if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
                return Response::send(-1, '演示账号禁止修改信息');
            }

            $username = trim(Input::get('username'));
            $password = trim(Input::get('password'));
            $roles = Input::get('roles', []);
            if (!is_array($roles)) {
                $roles = [];
            }

            $data = [];
            $data['name'] = trim(Input::get('name'));

            if (empty($username)) {
                return Response::send(-1, '用户名称不能为空');
            }

            if ($adminUserExists = ModelHelper::load(AdminUser::class, ['username' => $username])) {
                if (empty($adminUser) || $adminUserExists['id'] != $adminUser['id']) {
                    return Response::send(-1, '当前用户已经存在');
                }
            }

            if ($adminUser) {
                $adminUser = ModelHelper::updateOne(AdminUser::class, ['id' => $adminUser['id']], ['username' => $username]);
                AdminLogHelper::addInfoLogIfChanged('修改管理员', [
                    '用户名' => $adminUser['username'],
                ], [
                    '用户名' => $username,
                ]);
                if ($password) {
                    $adminUserService->changePwd($adminUser['id'], null, $password, true);
                    AdminLogHelper::addInfoLog('修改管理员密码', [
                        '用户名' => $username,
                    ]);
                }
            } else {
                if (empty($password)) {
                    return Response::send(-1, '密码不能为空');
                }
                AdminLogHelper::addInfoLog('添加管理员', [
                    '用户名' => $username,
                ]);
                $adminUser = ModelHelper::add(AdminUser::class, ['username' => $username]);
                $adminUserService->changePwd($adminUser['id'], null, $password, true);
            }

            ModelHelper::delete(AdminUserRole::class, ['userId' => $adminUser['id']]);
            foreach ($roles as $role) {
                ModelHelper::add(AdminUserRole::class, ['roleId' => $role, 'userId' => $adminUser['id']]);
            }

            ModelHelper::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

            return Response::send(0, null, null, '[js]$.dialogClose()');
        }

        $viewData = [];
        $viewData['adminUser'] = $adminUser;
        $viewData['roles'] = $roles;
        $viewData['adminUserRoleIds'] = $adminUserRoleIds;
        return view('admin::system.user.edit', $viewData);
    }

    public function userDelete($id = 0)
    {
        if (env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
            return Response::send(-1, '演示账号禁止修改信息');
        }

        if ($id == env('ADMIN_FOUNDER_ID', 1)) {
            return Response::send(-1, '创建者不能删除');
        }

        $adminUser = ModelHelper::load('admin_user', ['id' => $id]);
        if (empty($adminUser)) {
            return Response::send(-1, '记录不存在');
        }

        ModelHelper::delete(AdminUser::class, ['id' => $id]);
        ModelHelper::delete(AdminUserRole::class, ['userId' => $id]);

        ModelHelper::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

        AdminLogHelper::addInfoLog('删除管理员', [
            '用户名' => $adminUser['username'],
        ]);

        return Response::send(0, null, null, '[js]window.lister.load(false);');
    }

    public function logList()
    {
        if (Request::isMethod('post')) {

            $input = InputPackage::buildFromInput();
            $page = $input->getInteger('page', 10);

            $pageSize = 20;
            $option = [];

            $option['order'] = ['id', 'desc'];
            $option['search'] = $input->getArray('search');

            $head = [];
            $head[] = ['field' => 'created_at', 'title' => '时间', 'attr' => 'width="140"'];
            $head[] = ['field' => 'type', 'title' => '类型', 'attr' => 'width="80"'];
            $head[] = ['field' => 'adminUserName', 'title' => '用户',];
            $head[] = ['field' => 'summary', 'title' => '操作',];
            $head[] = ['field' => 'content', 'title' => '数据',];
            if (AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@logDelete')) {
                $head[] = ['field' => '_operation', 'title' => '-'];
            }

            $paginateData = ModelHelper::modelPaginate('admin_log', $page, $pageSize, $option);
            ModelHelper::modelJoin($paginateData['records'], 'adminUserId', '_adminUser', 'admin_user', 'id');
            ModelHelper::modelJoin($paginateData['records'], 'id', '_data', 'admin_log_data', 'id');

            $list = [];
            foreach ($paginateData['records'] as $record) {
                $item = ['_id' => $record['id']];
                switch ($record['type']) {
                    case AdminLogType::INFO:
                        $item['type'] = '<span class="uk-text-success">信息</span>';
                        break;
                    case AdminLogType::ERROR:
                        $item['type'] = '<span class="uk-text-danger">错误</span>';
                        break;
                }
                $item['created_at'] = $record['created_at'];
                if (empty($record['_adminUser'])) {
                    $item['adminUserName'] = '-';
                } else {
                    $item['adminUserName'] = $record['_adminUser']['username'];
                }
                $item['summary'] = htmlspecialchars($record['summary']);
                $item['content'] = '';
                if (!empty($record['_data']['content'])) {
                    $content = @json_decode($record['_data']['content'], true);
                    if (empty($content)) {
                        $content = $record['_data']['content'];
                    }
                    if (!empty($content)) {
                        $contentLines = [];
                        if (is_array($content)) {
                            foreach ($content as $k => $v) {
                                if (is_array($v)) {
                                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                                }
                                $contentLines[] = "<span class='uk-text-muted'>" . htmlspecialchars($k) . ":</span> " . htmlspecialchars($v);
                            }
                        } else {
                            $contentLines[] = htmlspecialchars($content);
                        }
                        $item['content'] = '<div style="max-height:50px;width:700px;display:inline-block;white-space:normal;line-height:15px;overflow:hidden;font-size:12px;border:1px solid #CCC;border-radius:3px;background:#EEE;padding:3px;box-sizing:content-box;cursor:pointer;" data-uk-tooltip title="点击展开/缩小" onclick="$(this).css(\'max-height\',$(this).css(\'max-height\')==\'50px\'?\'\':\'50px\');">' . join('<br />', $contentLines) . '</div>';
                    }
                }
                if (AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@logDelete')) {
                    $item['_operation'] = '<a href="javascript:;" data-ajax-request-loading data-ajax-request="delete/' . $record['id'] . '" data-uk-tooltip title="删除"><i class="uk-icon-trash"></i></a>';
                }
                $list[] = $item;
            }

            $data = [];
            $data['head'] = $head;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::json(0, null, $data);
        }
        return view('admin::system.log.list');
    }


    public function logDelete($id = 0)
    {
        if (DemoHelper::shouldDenyAdminDemo()) {
            return Response::send(-1, '演示账号禁止修改信息');
        }

        ModelHelper::delete('admin_log', ['id' => $id]);
        ModelHelper::delete('admin_log_data', ['id' => $id]);

        return Response::send(0, null, null, '[js]window.lister.load(false);');
    }

}