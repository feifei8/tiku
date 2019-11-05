@extends('theme.default.pc.frame')

@section('pageTitleMain','资讯')


@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>资讯</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-3-4">
                <div class="pb pb-news-list">
                    <div class="head">
                        <h2>
                            @if($categoryId)
                                @foreach($categories as $category)
                                    @if($category['id']==$categoryId)
                                        {{$category['name']}}
                                    @endif
                                @endforeach
                            @else
                                全部
                            @endif
                        </h2>
                    </div>
                    <div class="body">
                        <div class="list">
                            @foreach($news as $new)
                                <div class="item">
                                    <a class="title" href="/news/{{$new['id']}}">{{$new['title']}}</a>
                                    <div class="summary">
                                        {{\Illuminate\Support\Str::limit($new['summary'],200)}}
                                    </div>
                                    <div class="tool">
                                        <a class="more" href="/news/{{$new['id']}}">
                                            [阅读全文]
                                        </a>
                                        <div class="time">{{($new['updated_at'])}}</div>
                                    </div>
                                </div>
                            @endforeach
                            @if(empty($news))
                                <div class="empty">暂无记录</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="page-container">
                    {!! $pageHtml !!}
                </div>

            </div>
            <div class="uk-width-1-4">

                <div class="pb pb-news-category">
                    <div class="body">
                        <a href="/news" @if(!$categoryId) class="active" @endif >全部</a>
                        @foreach($categories as $category)
                            <a href="/news?category_id={{$category['id']}}"  @if($category['id']==$categoryId) class="active" @endif >{{$category['name']}}</a>
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





