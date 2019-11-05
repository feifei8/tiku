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
            <div class="question">
                {!! $questionData['question']['question'] !!}
            </div>
            <div class="answer" data-answer>
                <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                <div class="answer-body html-container">
                    {!! $questionData['answer']['answer'] !!}
                </div>
            </div>
            <div class="my-answer">
                @if(\App\Helpers\QuestionExamHelper::isTextCorrect($questionData['answer']['answer'],$paperExamQuestions[$paperQuestionIndex]['answer'][0]))
                    <div class="uk-alert uk-alert-success">
                        <div class="label">我的答案</div>
                        {!! $paperExamQuestions[$paperQuestionIndex]['answer'][0] !!}
                    </div>
                @else
                    <div class="uk-alert uk-alert-danger">
                        <div class="label">我的答案</div>
                        {!! $paperExamQuestions[$paperQuestionIndex]['answer'][0] !!}
                    </div>
                @endif
            </div>
            @if(\App\Helpers\QuestionHelper::hasContent($questionData['analysis']['analysis']))
                <div class="analysis" data-analysis>
                    <div class="analysis-head"><i class="uk-icon-list-alt"></i> 解析</div>
                    <div class="analysis-body html-container">
                        {!! $questionData['analysis']['analysis'] !!}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

