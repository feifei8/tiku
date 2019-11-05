<?php

namespace Edwin404\Tecmz\Traits;

use Edwin404\Base\Support\FileHelper;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\InputTypeHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Member\Services\MemberService;
use Edwin404\Tecmz\Helpers\MailHelper;
use Edwin404\Tecmz\Helpers\SmsHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mews\Captcha\Facades\Captcha;

trait MemberProfileTrait
{
    public function password(MemberService $memberService)
    {
        if (Request::isMethod('post')) {

            $input = InputPackage::buildFromInput();

            $passwordOld = $input->getTrimString('passwordOld');
            $passwordNew = $input->getTrimString('passwordNew');
            $passwordRepeat = $input->getTrimString('passwordRepeat');

            if ($passwordNew != $passwordRepeat) {
                return Response::send(-1, '两次新密码输入不一致');
            }

            $ret = $memberService->changePassword($this->memberUserId(), $passwordNew, $passwordOld);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }

            return Response::send(0, '修改成功', null, '[reload]');

        }
        return $this->_view('member.profile.password');
    }

    public function avatar(MemberService $memberService)
    {
        if (Request::isMethod('post')) {
            switch (Input::get('type')) {
                case 'cropper':
                    $avatar = Input::get('avatar');
                    if (empty($avatar)) {
                        return Response::send(-1, '头像内容为空');
                    }
                    if (!Str::startsWith($avatar, 'data:image/jpeg;base64,')) {
                        return Response::send(-1, '头像数据为空');
                    }
                    $avatar = substr($avatar, strlen('data:image/jpeg;base64,'));
                    $avatar = @base64_decode($avatar);
                    if (empty($avatar)) {
                        return Response::send(-1, '头像内容为空');
                    }
                    $ret = $memberService->setAvatar($this->memberUserId(), $avatar, 'jpg');
                    if ($ret['code']) {
                        return $ret;
                    }
                    return Response::send(0, '保存成功', null, '[reload]');
                    break;
                default:
                    $avatar = Input::get('avatar');
                    if (empty($avatar)) {
                        return Response::send(-1, '头像未修改');
                    }
                    $avatar = FileHelper::savePathToLocal($avatar);
                    if (empty($avatar)) {
                        return Response::send(-1, '读取头像文件失败:-1');
                    }
                    $avatarExt = FileHelper::extension($avatar);
                    if (!in_array($avatarExt, config('data.upload.image.extensions'))) {
                        return Response::send(-1, '头像格式不合法');
                    }
                    $avatar = file_get_contents($avatar);
                    if (empty($avatar)) {
                        return Response::send(-1, '读取头像文件失败:-2');
                    }
                    $ret = $memberService->setAvatar($this->memberUserId(), $avatar, $avatarExt);
                    if ($ret['code']) {
                        return $ret;
                    }
                    return Response::send(0, '保存成功', null, '[reload]');
            }
        }
        return $this->_view('member.profile.avatar');
    }

    public function captcha()
    {
        return Captcha::create('default');
    }

    public function email(MemberService $memberService)
    {
        if (Request::isMethod('post')) {

            $input = InputPackage::buildFromInput();
            $email = $input->getEmail('email');
            $verify = $input->getTrimString('verify');

            if (empty($email)) {
                return Response::send(-1, '邮箱不能为空');
            }
            if (!InputTypeHelper::isEmail($email)) {
                return Response::send(-1, '邮箱格式不正确');
            }
            if (empty($verify)) {
                return Response::send(-1, '验证码不能为空');
            }
            if ($verify != Session::get('memberProfileEmailVerify')) {
                return Response::send(-1, '验证码不正确');
            }
            if (Session::get('memberProfileEmailVerifyTime') + 60 * 60 < time()) {
                return Response::send(0, '验证码已过期');
            }
            if ($email != Session::get('memberProfileEmail')) {
                return Response::send(-1, '两次邮箱不一致');
            }

            $memberUserExists = $memberService->loadByEmail($email);
            if (!empty($memberUserExists)) {
                if ($memberUserExists['id'] != $this->memberUserId()) {
                    return Response::send(-1, '该邮箱已被其他账户绑定');
                }
                if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['email'] == $email) {
                    return Response::send(-1, '邮箱未修改，无需重新绑定。');
                }
            }

            $memberService->update($this->memberUserId(), [
                'emailVerified' => true,
                'email' => $email,
            ]);

            Session::forget('memberProfileEmailVerify');
            Session::forget('memberProfileEmailVerifyTime');
            Session::forget('memberProfileEmail');

            return Response::send(0, '修改成功', null, '[reload]');

        }
        return $this->_view('member.profile.email');
    }

    public function emailVerify(MemberService $memberService)
    {
        $email = Input::get('target');
        if (empty($email)) {
            return Response::send(-1, '邮箱不能为空');
        }
        if (!InputTypeHelper::isEmail($email)) {
            return Response::send(-1, '邮箱格式不正确');
        }

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误');
        }

        $memberUserExists = $memberService->loadByEmail($email);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != $this->memberUserId()) {
                return Response::send(-1, '该邮箱已被其他账户绑定');
            }
            if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['email'] == $email) {
                return Response::send(-1, '邮箱未修改，无需重新绑定。');
            }
        }

        if (Session::get('memberProfileEmailVerifyTime') && $email == Session::get('memberProfileEmail')) {
            if (Session::get('memberProfileEmailVerifyTime') + 60 * 10 > time()) {
                return Response::send(0, '验证码发送成功!');
            }
        }


        $verify = rand(100000, 999999);
        Session::put('memberProfileEmailVerify', $verify);
        Session::put('memberProfileEmailVerifyTime', time());
        Session::put('memberProfileEmail', $email);

        MailHelper::send($email, '验证码', 'verify', ['verify' => $verify]);

        return Response::send(0, '验证码发送成功');

    }

    public function phone(MemberService $memberService)
    {
        if (Request::isMethod('post')) {

            $input = InputPackage::buildFromInput();

            $phone = $input->getPhone('phone');
            $verify = $input->getTrimString('verify');

            if (empty($phone)) {
                return Response::send(-1, '手机不能为空');
            }
            if (!InputTypeHelper::isPhone($phone)) {
                return Response::send(-1, '手机格式不正确');
            }
            if (empty($verify)) {
                return Response::send(-1, '验证码不能为空');
            }
            if ($verify != Session::get('memberProfilePhoneVerify')) {
                return Response::send(-1, '验证码不正确');
            }
            if (Session::get('memberProfilePhoneVerifyTime') + 60 * 60 < time()) {
                return Response::send(0, '验证码已过期');
            }
            if ($phone != Session::get('memberProfilePhone')) {
                return Response::send(-1, '两次手机不一致');
            }

            $memberUserExists = $memberService->loadByPhone($phone);
            if (!empty($memberUserExists)) {
                if ($memberUserExists['id'] != $this->memberUserId()) {
                    return Response::send(-1, '该手机已被其他账户绑定');
                }
                if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['phone'] == $phone) {
                    return Response::send(-1, '手机号未修改，无需重新绑定。');
                }
            }

            $memberService->update($this->memberUserId(), [
                'phoneVerified' => true,
                'phone' => $phone,
            ]);

            Session::forget('memberProfilePhoneVerify');
            Session::forget('memberProfilePhoneVerifyTime');
            Session::forget('memberProfilePhone');

            return Response::send(0, '修改成功', null, '[reload]');

        }
        return $this->_view('member.profile.phone');
    }

    public function phoneVerify(MemberService $memberService)
    {
        $phone = Input::get('target');
        if (empty($phone)) {
            return Response::send(-1, '手机不能为空');
        }
        if (!InputTypeHelper::isPhone($phone)) {
            return Response::send(-1, '手机格式不正确');
        }

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误');
        }

        $memberUserExists = $memberService->loadByPhone($phone);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != $this->memberUserId()) {
                return Response::send(-1, '该手机已被其他账户绑定');
            }
            if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['phone'] == $phone) {
                return Response::send(-1, '手机号未修改，无需重新绑定。');
            }
        }

        if (Session::get('memberProfilePhoneVerifyTime') && $phone == Session::get('memberProfilePhone')) {
            if (Session::get('memberProfilePhoneVerifyTime') + 60 * 2 > time()) {
                return Response::send(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('memberProfilePhoneVerify', $verify);
        Session::put('memberProfilePhoneVerifyTime', time());
        Session::put('memberProfilePhone', $phone);

        SmsHelper::send($phone, 'verify', ['verify' => $verify]);

        return Response::send(0, '验证码发送成功');

    }

}