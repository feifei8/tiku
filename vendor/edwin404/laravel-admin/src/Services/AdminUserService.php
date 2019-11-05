<?php

namespace Edwin404\Admin\Services;

use Carbon\Carbon;
use Edwin404\Admin\Models\AdminUser;
use Edwin404\Admin\Type\AdminLogType;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminUserService
{

    private function passwordEncrypt($password, $passwordSalt)
    {
        return md5(md5($password) . md5($passwordSalt));
    }

    public function ruleChanged($id, $ruleChanged)
    {
        ModelHelper::update(AdminUser::class, ['id' => $id], ['ruleChanged' => boolval($ruleChanged)]);
    }

    public function login($username, $password)
    {
        $adminUser = ModelHelper::load(AdminUser::class, ['username' => $username]);
        if (empty($adminUser)) {
            return Response::generate(-1, "用户不存在");
        }
        if ($adminUser['password'] != $this->passwordEncrypt($password, $adminUser['passwordSalt'])) {
            return Response::generate(-2, "密码不正确");
        }
        return Response::generate(0, 'ok', $adminUser);
    }

    public function add($username, $password, $ignorePassword = false)
    {
        $passwordSalt = Str::random(16);
        $adminUser = new AdminUser();
        $adminUser->username = $username;
        if (!$ignorePassword) {
            $adminUser->passwordSalt = $passwordSalt;
            $adminUser->password = $this->passwordEncrypt($password, $passwordSalt);
        }
        $adminUser->save();
        return $adminUser->toArray();
    }

    public function getRolesByUserId($userId)
    {
        $adminUser = AdminUser::where('id', $userId)->first();
        if (empty($adminUser)) {
            return Response::generate(-1, "用户不存在");
        }
        $roleRules = [];
        foreach ($adminUser->roles as $role) {
            $rules = $role->toArray();
            $rules['rules'] = [];
            foreach ($role->rules as $rule) {
                $rules['rules'][] = $rule->toArray();
            }
            $roleRules[] = $rules;
        }
        return Response::generate(0, null, $roleRules);
    }

    public function load($id)
    {
        return ModelHelper::load(AdminUser::class, ['id' => $id]);
    }

    public function loadByUsername($username)
    {
        return ModelHelper::load(AdminUser::class, ['username' => $username]);
    }

    public function changePwd($id, $old, $new, $ignoreOld = false)
    {
        $adminUser = ModelHelper::load(AdminUser::class, ['id' => $id]);
        if (empty($adminUser)) {
            return Response::generate(-1, '用户不存在');
        }
        if ($adminUser['password'] != $this->passwordEncrypt($old, $adminUser['passwordSalt'])) {
            if (!$ignoreOld) {
                return Response::generate(-1, '旧密码不正确');
            }
        }

        $passwordSalt = Str::random(16);

        $data = [];
        $data['password'] = $this->passwordEncrypt($new, $passwordSalt);
        $data['passwordSalt'] = $passwordSalt;
        $data['lastChangePwdTime'] = Carbon::now();

        ModelHelper::update(AdminUser::class, ['id' => $adminUser['id']], $data);

        return Response::generate(0, 'ok');
    }

    public function addInfoLog($adminUserId, $summary, $content = [])
    {
        static $exists = null;
        if (null === $exists) {
            $exists = Schema::hasTable('admin_log');
        }
        if (!$exists) {
            return;
        }
        $adminLog = ModelHelper::add('admin_log', ['adminUserId' => $adminUserId, 'type' => AdminLogType::INFO, 'summary' => $summary]);
        if (!empty($content)) {
            ModelHelper::add('admin_log_data', ['id' => $adminLog['id'], 'content' => json_encode($content)]);
        }
    }

    public function addErrorLog($adminUserId, $summary, $content = [])
    {
        static $exists = null;
        if (null === $exists) {
            $exists = Schema::hasTable('admin_log');
        }
        if (!$exists) {
            return;
        }
        $adminLog = ModelHelper::add('admin_log', ['adminUserId' => $adminUserId, 'type' => AdminLogType::ERROR, 'summary' => $summary]);
        if (!empty($content)) {
            ModelHelper::add('admin_log_data', ['id' => $adminLog['id'], 'content' => json_encode($content)]);
        }
    }

    public function addInfoLogIfChanged($adminUserId, $summary, $old, $new)
    {
        $changed = [];
        if (empty($old) && empty($new)) {
            return;
        }
        foreach ($old as $k => $oldValue) {
            if (!array_key_exists($k, $new)) {
                $changed['删除:' . $k . ':原值'] = $oldValue;
                continue;
            }
            if ($new[$k] != $oldValue) {
                $changed['修改:' . $k . ':原值'] = $oldValue;
                continue;
            }
        }
        foreach ($new as $k => $newValue) {
            if (!array_key_exists($k, $old)) {
                $changed['新增:' . $k . ':新值'] = $newValue;
                continue;
            }
        }
        if (empty($changed)) {
            return;
        }
        $this->addInfoLog($adminUserId, $summary, $changed);
    }

}