@if(!empty($paperQuestionNumber))
    <div class="pb pb-question-view-number">
        <div>
            @if($paperQuestionNumberCount>1)
                第 {{$paperQuestionNumber}}-{{$paperQuestionNumber+$paperQuestionNumberCount-1}} 题
            @else
                第 {{$paperQuestionNumber}} 题
            @endif
            &nbsp;&nbsp;
            <i class="uk-icon-comment-o"></i>问答题
        </div>
    </div>
@endif
<div class="pb pb-question-view">
    @if(empty($paperQuestionNumber))
        <div class="head">
            <h2>
                问答题
            </h2>
        </div>
    @endif
    <div class="body">

        <div class="question" data-question="text" data-alias="{{$questionData['question']['alias']}}">
            <div class="question html-container">
                {!! $questionData['question']['question'] !!}
            </div>
            <div class="action">
                <a href="javascript:;" data-answer-show>查看答案</a>
            </div>
            @if($_memberUserId)
                <div class="answer" data-answer>
                    <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                    <div class="answer-body html-container">
                        {!! $questionData['answer']['answer'] !!}
                    </div>
                </div>
                @if($questionData['analysis']['analysis'])
                    <div class="analysis" data-analysis>
                        <div class="analysis-head"><i class="uk-icon-list-alt"></i> 解析</div>
                        <div class="analysis-body html-container">
                            {!! $questionData['analysis']['analysis'] !!}
                        </div>
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>

