@extends('theme.default.pc.frame')

@section('pageTitleMain',htmlspecialchars($news['title']))

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/news">资讯</a></li>
                <li class="uk-active"><span>{{$news['title']}}</span></li>
            </ul>
        </div>


        <div class="uk-grid">
            <div class="uk-width-3-4">

                <div class="pb pb-news-view">
                    <div class="body">
                        @include('public.shareButtons')
                        <h1>{{$news['title']}}</h1>
                        <div class="attr">
                            时间：{{($news['created_at'])}}
                            分类：{{$news['_category']['name']}}
                        </div>
                        <div class="content html-container">
                            {!! \Edwin404\Base\Support\HtmlHelper::replaceImageSrcToLazyLoad($news['content'],'data-src',true) !!}
                        </div>
                    </div>
                </div>

            </div>
            <div class="uk-width-1-4">

                <div class="pb pb-news-latest">
                    <div class="head">
                        <h2>最近资讯</h2>
                    </div>
                    <div class="body">
                        @foreach($newsLatest as $new)
                            <a class="item" href="/news/{{$new['id']}}">
                                <span class="time">{{\Carbon\Carbon::parse($new['created_at'])->format('m-d')}}</span> {{$new['title']}}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if(!empty($ads))
                    <div class="pb pb-recommend">
                        <div class="body">
                            @foreach($ads as $ad)
                                @if($ad['link'])
                                    <a class="item" href="{{$ad['link']}}" target="_blank">
                                        <img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($ad['image'])}}" />
                                    </a>
                                @else
                                    <div class="item">
                                        <img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($ad['image'])}}" />
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>

@endsection





