@extends('theme.default.pc.frame')

@section('pageTitle',htmlspecialchars($pageTitle))
@section('pageKeywords',htmlspecialchars($pageKeywords))
@section('pageDescription',htmlspecialchars($pageDescription))

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>题目</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-3-4">

                @if(!empty($selectedTags) || $questionType)
                    <div class="pb pb-question-tag">
                        <div class="body">
                            标签：
                            @if($questionType)
                                <span>
                                    {{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$questionType)}}
                                    <a href="/question/list/{{\Edwin404\Base\Support\TagHelper::urlJoin(($keyword?'k'.urlencode($keyword):''),$selectedTagsIds)}}"><i class="uk-icon-remove"></i></a>
                                </span>
                            @endif
                            @foreach($selectedTags as $tag)
                                <span>
                                    {{$tag['title']}}
                                    <?php
                                        $url = [];
                                        if($questionType){
                                            $url[] = 't'.$questionType;
                                        }
                                        if($keyword){
                                            $url[] = 'k'.urlencode($keyword);
                                        }
                                    ?>
                                    <a href="/question/list/{{\Edwin404\Base\Support\TagHelper::urlJoin(join('_',$url),$selectedTagsIds,$tag['id'])}}"><i class="uk-icon-remove"></i></a>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pb pb-question-list">
                    <div class="head">
                        <h2>题目列表</h2>
                    </div>
                    <div class="body">
                        @if(empty($questions))
                            <div class="empty">
                                暂无记录
                            </div>
                        @endif
                        @foreach($questions as $question)
                            <div class="item">
                                <div class="title">
                                    <a href="/question/view/{{$question['alias']}}?param={{urlencode($urlParam)}}">
                                        [{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$question['type'])}}]
                                        {{\Edwin404\Base\Support\HtmlHelper::text($question['question'],100)}}
                                    </a>
                                </div>
                                <div class="tags">
                                    <div class="right">
                                        @if($question['source'])
                                            <span>来源 {{$question['source']}}</span>
                                            |
                                        @endif
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

            </div>
            <div class="uk-width-1-4">

                <div class="pb pb-category">
                    <div class="body">
                        <a class="title" href="javascript:;">类型</a>
                        <div class="title-box">
                            <?php
                            $url = [];
                            if($keyword){
                                $url[] = 'k'.urlencode($keyword);
                            }
                            ?>
                            <a @if(empty($questionType)) class="active" @endif href="/question/list/{{\Edwin404\Base\Support\TagHelper::urlJoin(join('_',$url),$selectedTagsIds)}}">全部</a>
                            @foreach(\App\Types\QuestionType::getList() as $k=>$v)
                                <?php
                                $url = [];
                                $url[] = 't'.$k;
                                if($keyword){
                                    $url[] = 'k'.urlencode($keyword);
                                }
                                ?>
                                <a href="/question/list/{{\Edwin404\Base\Support\TagHelper::urlJoin(join('_',$url),$selectedTagsIds)}}"
                                   @if($questionType==$k) class="active" @endif>{{$v}}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pb pb-category">
                    <div class="body">
                        @foreach($tags as $tagGroup)
                            <a class="title" href="javascript:;">{{$tagGroup['groupTitle']}}</a>
                            <div class="title-box">
                                @foreach($tagGroup['groupTags'] as $tag)
                                    <?php
                                    $url = [];
                                    if($questionType){
                                        $url[] = 't'.$questionType;
                                    }
                                    if($keyword){
                                        $url[] = 'k'.urlencode($keyword);
                                    }
                                    ?>
                                    @if(in_array($tag['id'],$selectedTagsIds))
                                        <a href="/question/list/{{\Edwin404\Base\Support\TagHelper::urlJoin(join('_',$url),$selectedTagsIds,$tag['id'])}}" class="active">{{$tag['title']}}</a>
                                    @else
                                        <a href="/question/list/{{\Edwin404\Base\Support\TagHelper::urlJoin(join('_',$url),array_merge($selectedTagsIds,[$tag['id']]))}}">{{$tag['title']}}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>


@endsection