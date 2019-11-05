@extends('base::frame')

@section('pageTitle','页面出错了')

@section('headAppend')
    <style type="text/css">
        body, html {background: #EEE;font:13px/20px normal Helvetica, Arial, "微软雅黑", sans-serif;color:#999;padding:0;margin:0;}
        h1{color:#CCC;font-size:60px;padding:40px 0 0 0;margin:0;text-align:center;}
        .content{padding-top:2em;text-align:center;}
        .suggest{padding-top:2em;text-align:center;line-height:20px;}
        .suggest a{color:#3385ff;display:inline-block;padding:0 10px;line-height:20px;border-radius:3px;text-decoration:none;}
    </style>
@endsection

@section('body')
    <h1>
        500
    </h1>
    <div class="content">
        <p>很抱歉，您访问的页面出出现错误了</p>
        <p>程序猿已被关入小黑屋正在修复~</p>
    </div>
    <div class="suggest">
        <p>您可以 <a href="javascript:;" onclick="window.location.reload();">刷新页面</a> 再试试 或 <a href="/">访问首页</a> </p>
    </div>
@endsection
