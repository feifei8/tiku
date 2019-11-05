@extends('theme.default.m.frame')

@section('pageTitle',$pageTitle)
@section('pageTitleMain','搜索题目 “'.htmlspecialchars($keywords).'”')
@section('pageKeywords',$pageKeywords)
@section('pageDescription',$pageDescription)

@section('bodyContent')

    @if(0)
    <div class="pb pb-search-tab">
        <div class="body">
            <a href="/search/question?keywords={{urlencode($keywords)}}" class="active">题目</a>
            <a href="/search/paper?keywords={{urlencode($keywords)}}">试卷</a>
        </div>
    </div>
    @endif

    <div class="pb-question-list">
        <div class="body">
            @if(empty($records))
                <div class="empty">
                    暂无记录
                </div>
            @endif
            @foreach($records as $question)
                <div class="item">
                    <div class="title">
                        <a href="/question/view/{{$question['alias']}}">
                            [{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$question['type'])}}]
                            {{\Edwin404\Base\Support\HtmlHelper::text($question['question'],100)}}
                        </a>
                    </div>
                    <div class="tags">
                        <div class="right">
                            <span>正确率 {{$question['testCount']>0?sprintf('%d%%',$question['passCount']*100/$question['testCount']):'-'}}</span>
                            |
                            <span>评论 {{$question['commentCount'] or 0}}</span>
                            |
                            <span>点击 {{$question['clickCount']}}</span>
                        </div>
                        @foreach($question['tag'] as $tag)
                            <a href="/question/list/{{$tag['id']}}" target="_blank">{{$tag['title']}}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="page-container">
                {!! $pageHtml !!}
            </div>
        </div>
    </div>


@endsection