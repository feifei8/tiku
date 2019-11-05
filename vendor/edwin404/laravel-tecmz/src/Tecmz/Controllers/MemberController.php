<?php

namespace Edwin404\Tecmz\Controllers;

use Edwin404\Admin\Cms\Field\FieldImage;
use Edwin404\Admin\Cms\Field\FieldSecret;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Handle\BasicCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\Response;
use Edwin404\Member\Events\MemberUserRegisteredEvent;
use Edwin404\Member\Services\MemberService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MemberController extends AdminCheckController
{
    protected $cmsConfigData = [
        'model' => 'member_user',
        'pageTitle' => '用户列表',
        'group' => 'data',
        'canView' => true,
        'canAdd' => true,
        'canExport' => true,
        'fields' => [
            'created_at' => ['type' => FieldText::class, 'title' => '加入时间', 'list' => true, 'export' => true,],
            'avatar' => ['type' => FieldImage::class, 'title' => '头像', 'list' => true,],
            'username' => ['type' => FieldText::class, 'title' => '用户名', 'list' => true, 'search' => true, 'add' => true, 'export' => true,],
            'email' => ['type' => FieldText::class, 'title' => '邮箱', 'list' => true, 'search' => true, 'add' => true, 'export' => true,],
            'phone' => ['type' => FieldText::class, 'title' => '手机', 'list' => true, 'search' => true, 'add' => true, 'export' => true,],
            'password' => ['type' => FieldText::class, 'title' => '密码', 'add' => true,],
        ]
    ];

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    public function dataAdd(BasicCms $basicCms, MemberService $memberService)
    {
        if (Request::isMethod('post')) {

            if (Session::get('_adminUserId', null) && env('ADMIN_DEMO_USER_ID', 0) && Session::get('_adminUserId', null) == env('ADMIN_DEMO_USER_ID', 0)) {
                return Response::send(-1, '演示账号禁止该操作');
            }

            $ret = $memberService->register(
                Input::get('username'),
                Input::get('phone'),
                Input::get('email'),
                Input::get('password')
            );

            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }

            $memberUser = $ret['data'];
            if (method_exists($this, 'hookMemberRegistered')) {
                $this->hookMemberRegistered($memberUser);
            }

            Event::fire(new MemberUserRegisteredEvent($memberUser['id']));

            return Response::send(0, null, null, '[js]parent.api.dialog.dialogClose(parent.__dialogData.add);');

        }
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

    public function dataView(MemberService $memberService,
                             $memberUserId = 0)
    {
        if (Request::isMethod('post') && env('ADMIN_DEMO_USER_ID', 0) && $this->adminUserId() == env('ADMIN_DEMO_USER_ID', 0)) {
            return Response::send(-1, '演示账号禁止修改信息');
        }

        if (empty($memberUserId)) {
            $memberUserId = Input::get('_id');
        }
        $memberUser = $memberService->load($memberUserId);
        if (empty($memberUser)) {
            return Response::send(-1, '用户为空');
        }
        if (Request::isMethod('post')) {

            $update = [];
            $username = trim(Input::get('username'));
            if ($memberUser['username'] != $username) {
                $update['username'] = $username;
            }
            $phone = trim(Input::get('phone'));
            if ($memberUser['phone'] != $phone) {
                $update['phone'] = $phone;
            }
            $email = trim(Input::get('email'));
            if ($memberUser['email'] != $email) {
                $update['email'] = $email;
            }
            if (!empty($update)) {
                $memberService->update($memberUser['id'], $update);
            }

            $resetPassword = trim(Input::get('resetPassword'));
            if ($resetPassword) {
                $memberService->changePassword($memberUserId, $resetPassword, null, true);
                return Response::send(0, '密码已经成功修改为"' . $resetPassword . '"', null, '[js]$.dialogClose();');
            } else {
                return Response::send(0, null, null, '[js]$.dialogClose();');
            }
        }
        return view('common::member.admin.view', compact('memberUser', 'memberUserId'));
    }

    public function dataExport(BasicCms $basicCms)
    {
        return $basicCms->executeExport($this, $this->cmsConfigData);
    }

    public function enter($id)
    {
        Session::put('memberId', $id);
        return Response::send(0, null, null, '/');
    }

}