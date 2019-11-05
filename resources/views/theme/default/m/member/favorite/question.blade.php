@extends($_frameLayoutView)

@section('pageTitleMain','收藏的题目')
@section('footer')@endsection

@section('bodyContent')

    <div class="pb-question-list">
        <div class="body">
            @if(empty($records))
                <div class="empty">
                    暂无记录
                </div>
            @endif
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
                            <span>正确率 {{$record['_question']['testCount']>0?sprintf('%d%%',$record['_question']['passCount']*100/$record['_question']['testCount']):'-'}}</span>
                            |
                            <span>评论 {{$record['_question']['commentCount'] or 0}}</span>
                            |
                            <span>点击 {{$record['_question']['clickCount']}}</span>
                        </div>
                        <div data-favorite-action>
                            <a href="javascript:;" data-action="favorite" data-category="question" data-category-id="{{$record['_question']['id']}}" style="display:none;">
                                <span class="iconfont">&#xe673;</span>
                                点击收藏
                            </a>
                            <a href="javascript:;" data-action="unfavorite" data-category="question" data-category-id="{{$record['_question']['id']}}">
                                <span class="iconfont">&#xe673;</span>
                                取消收藏
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="page-container">
                {!! $pageHtml !!}
            </div>
        </div>
    </div>

@endsection