@extends($_frameLayoutView)

@section('bodyScript')
    <script>
        var __app = {
            isLogin:{{$_memberUserId?'true':'false'}},
            commentPostUrl:'/question/comment_post/{{$questionData['question']['alias']}}',
            loginRedirect:'{{urlencode(\Edwin404\Base\Support\RequestHelper::currentPageUrl())}}'
        };
    </script>
    <script src="@assets('assets/m/default/question.js')"></script>
@endsection

@section('pageTitleMain',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('pageTitle',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('pageKeywords',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('pageDescription',\Edwin404\Base\Support\HtmlHelper::text($questionData['question']['question']))
@section('footer')@endsection

@section('bodyContent')

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

    <div class="pb pb-question-info">
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
                        <span class="iconfont">&#xe673;</span>
                        <div class="text">收藏</div>
                    </a>
                    <a class="action active" href="javascript:;" data-action="unfavorite" data-category="question" data-category-id="{{$questionData['question']['id']}}" @if(!$hasFavorite) style="display:none;" @endif>
                        <span class="iconfont">&#xe673;</span>
                        <div class="text">已收藏</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($comments))
        <div class="pb pb-question-comment">
            <div class="head">
                <h2>用户评论</h2>
            </div>
            <div class="body">
                @foreach($comments as $comment)
                    <div class="item">
                        <div class="user">
                            <a href="javascript:;" class="avatar">
                                @if(empty($comment['_memberUser']['avatar']))
                                    <img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix('assets/lib/img/avatar.png')}}">
                                @else
                                    <img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($comment['_memberUser']['avatar'])}}">
                                @endif
                            </a>
                            <a href="javascript:;">{{$comment['_memberUser']['username'] or '[已删除用户]'}}</a>
                            发表于
                            <time datetime="{{$comment['created_at']}}"></time>
                        </div>
                        <div class="content">
                            {!! $comment['content'] !!}
                        </div>
                        @if($_memberUserId==$comment['memberUserId'])
                            <div class="action">
                                <a href="javascript:;" data-confirm="确定删除?" data-ajax-request-loading data-ajax-request="/question/comment_delete/{{$comment['id']}}">删除</a>
                            </div>
                        @endif
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
                <div id="content"></div>
            @else
                <div style="padding:50px 0;color:red;text-align:center;">请 <a href="/login?redirect={{urlencode(\Edwin404\Base\Support\RequestHelper::currentPageUrl())}}">登录</a> 后再回复</div>
            @endif
        </div>
    </div>

    @if($previousQuestion || $nextQuestion)
        <div style="height:50px;margin-top:10px;"></div>
        <div class="pb-question-nav">
            <div class="body">
                @if($previousQuestion)
                    <a href="/question/view/{{$previousQuestion['alias']}}?param={{urlencode($param)}}}">
                        上一题
                    </a>
                @endif
                @if($nextQuestion)
                    <a href="/question/view/{{$nextQuestion['alias']}}?param={{urlencode($param)}}}">
                        下一题
                    </a>
                @endif
            </div>
        </div>
    @endif

@endsection