@extends('tecmz::mail.frame')

@section('pageTitle','登录提醒')

@section('bodyContent')
    <p>尊敬的 {{$username or '{username}'}} 您好：</p>
    <p>&nbsp;</p>
    <p>您在最近登录了 {{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}，登录时间：{{\Carbon\Carbon::now()}}。</p>
    <p>&nbsp;</p>
    <p>{{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}安全团队</p>
@endsection