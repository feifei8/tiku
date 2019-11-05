@extends('theme.default.pc.frame')

@section('pageTitle',$pageTitle)
@section('pageKeywords',$pageKeywords)
@section('pageDescription',$pageDescription)

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>搜索试卷 “{{$keywords}}”</span></li>
            </ul>
        </div>

        <div class="pb pb-search-tab">
            <div class="body">
                <a href="/search/question?keywords={{urlencode($keywords)}}">题目</a>
                <a href="/search/paper?keywords={{urlencode($keywords)}}" class="active">试卷</a>
            </div>
        </div>

        <div class="pb pb-paper-list">
            <div class="head">
                <h2>搜索结果</h2>
            </div>
            <div class="body">
                <div class="list">
                    @if(empty($records))
                        <div class="empty">
                            暂无记录
                        </div>
                    @endif
                    @foreach($records as $paper)
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
                </div>
                <div class="page-container">
                    {!! $pageHtml !!}
                </div>
            </div>
        </div>

    </div>


@endsection