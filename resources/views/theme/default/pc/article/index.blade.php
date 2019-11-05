@extends('theme.default.pc.frame')

@section('pageTitle',htmlspecialchars($article['title']).' - '.htmlspecialchars(\Edwin404\Config\Facades\ConfigFacade::get('siteName')))

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>{{$article['title']}}</span></li>
            </ul>
        </div>

        <div class="pb pb-article">
            <div class="container">
                <h1>{{$article['title']}}</h1>
                <div class="content">
                    {!! $article['content'] !!}
                </div>
            </div>
        </div>
    </div>

@endsection





