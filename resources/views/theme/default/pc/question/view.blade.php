@extends('theme.default.pc.frame')

@section('pageTitle',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('pageTitle',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('pageKeywords',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('pageDescription',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))

@section('bodyScript')
    <script>
        var __app = {
            isLogin:{{$_memberUserId?'true':'false'}},
            loginRedirect:'{{urlencode(\Edwin404\Base\Support\RequestHelper::currentPageUrl())}}'
        };
    </script>
    <script src="@assets('assets/main/default/question.js')"></script>
@endsection

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/question">题目</a></li>
                <li class="uk-active"><span>{{\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question'],50)}}</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-3-4">
                @if($questionData['question']['type']==\App\Types\QuestionType::SINGLE_CHOICE)
                    @include('theme.default.pc.question.viewSingleChoice')
                @elseif($questionData['question']['type']==\App\Types\QuestionType::MULTI_CHOICES)
                    @include('theme.default.pc.question.viewMultiChoices')
                @elseif($questionData['question']['type']==\App\Types\QuestionType::TRUE_FALSE)
                    @include('theme.default.pc.question.viewTrueFalse')
                @elseif($questionData['question']['type']==\App\Types\QuestionType::FILL)
                    @include('theme.default.pc.question.viewFill')
                @elseif($questionData['question']['type']==\App\Types\QuestionType::TEXT)
                    @include('theme.default.pc.question.viewText')
                @elseif($questionData['question']['type']==\App\Types\QuestionType::GROUP)
                    @include('theme.default.pc.question.viewGroup')
                @endif
                @if(!empty($comments))
                    <div class="pb pb-question-comment">
                        <div class="head">
                            <h2>用户评论</h2>
                        </div>
                        <div class="body">
                            @foreach($comments as $comment)
                                <div class="item">
                                    <a href="javascript:;" class="avatar">
                                        @if(empty($comment['_memberUser']['avatar']))
                                            <img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix('assets/lib/img/avatar.png')}}">
                                        @else
                                            <img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($comment['_memberUser']['avatar'])}}">
                                        @endif
                                    </a>
                                    <div class="user">
                                        <a href="javascript:;">{{$comment['_memberUser']['username'] or '[已删除用户]'}}</a>
                                        发表于
                                        <time datetime="{{$comment['created_at']}}"></time>
                                    </div>
                                    <div class="content">
                                        {!! $comment['content'] !!}
                                    </div>
                                    <div class="action">
                                        @if($_memberUserId==$comment['memberUserId'])
                                            <a href="javascript:;" data-confirm="确定删除?" data-ajax-request-loading data-ajax-request="/question/comment_delete/{{$comment['id']}}">删除</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="pb pb-question-comment-post">
                    <div class="head">
                        <h2>发表评论</h2>
                    </div>
                    <div class="body">
                        @if($_memberUserId)
                            <form action="/question/comment_post/{{$questionData['question']['alias']}}" data-ajax-form method="post">
                                <div class="form">
                                    <div>
                                        <script type="text/plain" id="content" name="content"></script>
                                    </div>
                                </div>
                                <div class="button">
                                    <button type="submit" class="uk-button uk-button-main">发表回复</button>
                                </div>
                            </form>
                        @else
                            <div class="uk-text-center uk-text-danger" style="padding:50px 0;">请 <a href="/login?redirect={{urlencode(\Edwin404\Base\Support\RequestHelper::currentPageUrl())}}">登录</a> 后再回复</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="uk-width-1-4">

                <div class="pb pb-question-info">
                    <div class="head">
                        <h2>题目信息</h2>
                    </div>
                    <div class="body">

                        @if(!empty($questionData['question']['tag']))
                            <div class="tags">
                                @foreach($questionData['question']['tag'] as $tag)
                                    <a href="/question/list/{{$tag['id']}}" target="_blank">{{$tag['title']}}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="attr">
                            <a href="javascript:;">正确率 {{$questionData['question']['testCount']>0?sprintf('%d%%',$questionData['question']['passCount']*100/$questionData['question']['testCount']):'-'}}</a>
                            &nbsp;
                            |
                            &nbsp;
                            <a href="javascript:;">评论 {{$questionData['question']['commentCount'] or 0}}</a>
                            &nbsp;
                            |
                            &nbsp;
                            <a href="javascript:;">点击 {{$questionData['question']['clickCount']}}</a>
                        </div>

                    </div>
                </div>

                <div class="pb pb-block-action">
                    <div class="body">
                        <div class="uk-grid">
                            <div class="uk-width-1-1" data-favorite-action>
                                <a class="action" href="javascript:;" data-action="favorite" data-category="question" data-category-id="{{$questionData['question']['id']}}" @if($hasFavorite) style="display:none;" @endif>
                                    <i class="uk-icon-heart-o"></i>
                                    <div class="text">收藏</div>
                                </a>
                                <a class="action active" href="javascript:;" data-action="unfavorite" data-category="question" data-category-id="{{$questionData['question']['id']}}" @if(!$hasFavorite) style="display:none;" @endif>
                                    <i class="uk-icon-heart"></i>
                                    <div class="text">已收藏</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($previousQuestion || $nextQuestion)
                    <div class="pb pb-question-nav">
                        <div class="body">
                            @if($previousQuestion)
                                <a href="/question/view/{{$previousQuestion['alias']}}?param={{urlencode($param)}}}">
                                    上一题：
                                    [{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$previousQuestion['type'])}}]
                                    {{\Edwin404\Base\Support\HtmlHelper::text($previousQuestion['question'],100)}}
                                </a>
                            @endif
                            @if($nextQuestion)
                                <a href="/question/view/{{$nextQuestion['alias']}}?param={{urlencode($param)}}}">
                                    下一题：
                                    [{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$nextQuestion['type'])}}]
                                    {{\Edwin404\Base\Support\HtmlHelper::text($nextQuestion['question'],100)}}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

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