@if(!empty($paperQuestionNumber))
    <div class="pb pb-question-view-number">
        <div>
            @if($paperQuestionNumberCount>1)
                第 {{$paperQuestionNumber}}-{{$paperQuestionNumber+$paperQuestionNumberCount-1}} 题
            @else
                第 {{$paperQuestionNumber}} 题
            @endif
            &nbsp;&nbsp;
            <i class="uk-icon-pencil"></i>填空题
        </div>
    </div>
@endif
<div class="pb pb-question-view">
    @if(empty($paperQuestionNumber))
        <div class="head">
            <h2>
                填空题
            </h2>
        </div>
    @endif
    <div class="body">

        <div class="question" data-question="fill" data-alias="{{$questionData['question']['alias']}}">
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
                        @foreach($questionData['answers'] as $answer)
                            {!! $answer['answer'] !!}
                            <br>
                        @endforeach
                    </div>
                </div>
                @if($questionData['analysis']['analysis'])
                    <div class="analysis" data-answer>
                        <div class="analysis-head"><i class="uk-icon-list-alt"></i> 解析</div>
                        <div class="analysis-body">
                            {!! $questionData['analysis']['analysis'] !!}
                        </div>
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
