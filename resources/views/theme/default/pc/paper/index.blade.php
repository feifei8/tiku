@extends('theme.default.pc.frame')

@section('pageTitleMain','试卷')

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>试卷</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-3-4">
                <div class="pb pb-paper-list">
                    <div class="head">
                        <h2>
                            公开试卷
                        </h2>
                    </div>
                    <div class="body">
                        <div class="list">
                            @if(!empty($papers))
                                @foreach($papers as $paper)
                                    <div class="item">
                                        <a class="title" href="/paper/view/{{$paper['alias']}}">{{$paper['title']}}</a>
                                        <div class="tool">
                                            <div class="attr">
                                                <div class="line">
                                                    <i class="uk-icon-certificate"></i> 题目总数：{{$paper['questionCount']}}
                                                </div>
                                                <div class="line">
                                                    <i class="uk-icon-check-circle-o"></i> 总分数：{{$paper['totalScore']}}
                                                </div>
                                                <div class="line">
                                                    @if($paper['timeLimitEnable'])
                                                        <i class="uk-icon-clock-o"></i> 答题时间：{{$paper['timeLimitValue']}}分钟
                                                    @else
                                                        <i class="uk-icon-clock-o"></i> 答题时间：不限时
                                                    @endif
                                                </div>
                                                @if($paper['_category'])
                                                    <div class="line">
                                                        <i class="uk-icon-th-large"></i> {{$paper['_category']['name']}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="action">
                                            <a href="/paper/view/{{$paper['alias']}}"><i class="uk-icon-paper-plane"></i> 进入练习</a>
                                            @if($_memberUserId)
                                                <a class="exam" href="/paper/exam/{{$paper['alias']}}"><i class="uk-icon-gavel"></i> 参加考试</a>
                                            @else
                                                <a class="exam" href="/login?redirect={{urlencode('/paper/exam/'.$paper['alias'].'')}}"><i class="uk-icon-gavel"></i> 登录并参加考试</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            @if(empty($papers))
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

                <div class="pb pb-paper-category">
                    <div class="body">
                        <a href="/paper" @if(!$categoryId) class="active" @endif >全部</a>
                        @foreach($paperCategories as $category)
                            <a href="/paper?category_id={{$category['id']}}"  @if($category['id']==$categoryId) class="active" @endif >{{$category['name']}}</a>
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