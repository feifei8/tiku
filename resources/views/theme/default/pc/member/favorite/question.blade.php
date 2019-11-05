@extends('theme.default.pc.frame')

@section('pageTitleMain','收藏的题目')


@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/member/{{$_memberUser['id']}}">我的中心</a></li>
                <li class="uk-active"><span>收藏的题目</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-6">
                @include('theme.default.pc.member.profile.menu')
            </div>
            <div class="uk-width-5-6">

                <div class="pb pb-question-list">
                    <div class="head">
                        <h2>收藏的题目</h2>
                    </div>
                    <div class="body">
                        @if(empty($records))
                            <div class="empty">
                                没有收藏任何题目~
                            </div>
                        @else
                            @foreach($records as $record)
                                <div class="item">
                                    <div class="title">
                                        <a href="/question/view/{{$record['_question']['alias']}}">
                                            [{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$record['_question']['type'])}}]
                                            {{\Edwin404\Base\Support\HtmlHelper::text($record['_question']['question'],100)}}
                                        </a>
                                    </div>
                                    <div class="tags">
                                        <div class="right">
                                            @if($record['_question']['source'])
                                                <span>来源 {{$record['_question']['source']}}</span>
                                                |
                                            @endif
                                            <span>正确率 {{$record['_question']['testCount']>0?sprintf('%d%%',$record['_question']['passCount']*100/$record['_question']['testCount']):'-'}}</span>
                                            |
                                            <span>评论 {{$record['_question']['commentCount'] or 0}}</span>
                                            |
                                            <span>点击 {{$record['_question']['clickCount']}}</span>
                                        </div>
                                        <div data-favorite-action>
                                            <a href="javascript:;" data-action="favorite" data-category="question" data-category-id="{{$record['_question']['id']}}" style="display:none;">
                                                <i class="uk-icon-heart-o"></i>
                                                点击收藏
                                            </a>
                                            <a href="javascript:;" data-action="unfavorite" data-category="question" data-category-id="{{$record['_question']['id']}}">
                                                <i class="uk-icon-heart"></i>
                                                取消收藏
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="page-container">
                    {!! $pageHtml !!}
                </div>

            </div>
        </div>

    </div>



@endsection