@extends('tecmz::mail.frame')

@section('pageTitle','注册成功')

@section('bodyContent')
    <p>尊敬的 {{$username or '{username}'}} 您好：</p>
    <p>&nbsp;</p>
    <p>欢迎您注册{{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}，此邮箱将为当您忘记密码时候找回密码的邮箱，请妥善保管。</p>
    <p>&nbsp;</p>
    <p>{{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}团队</p>
@endsection