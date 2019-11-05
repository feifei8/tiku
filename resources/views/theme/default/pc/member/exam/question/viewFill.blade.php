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
<div class="pb pb-question-view">
    <div class="body">

        <div class="question" data-question="fill" data-alias="{{$questionData['question']['alias']}}">
            <div class="question">
                {!! $questionData['question']['question'] !!}
            </div>
            <div class="answer" data-answer>
                <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                <div class="answer-body html-container">
                    @foreach($questionData['answers'] as $answer)
                        {!! $answer['answer'] !!}
                        <br>
                    @endforeach
                </div>
            </div>
            <div class="my-answer">
                @foreach($paperExamQuestions[$paperQuestionIndex]['answer'] as $index=>$answer)
                    @if(\App\Helpers\QuestionExamHelper::isTextCorrect($questionData['answers'][$index]['answer'],$answer))
                        <div class="uk-alert uk-alert-success">
                            <div class="label">我的答案{{$index+1}}</div>
                            {!! $answer !!}
                        </div>
                    @else
                        <div class="uk-alert uk-alert-danger">
                            <div class="label">我的答案{{$index+1}}</div>
                            {!! $answer !!}
                        </div>
                    @endif
                @endforeach
            </div>
            @if(\App\Helpers\QuestionHelper::hasContent($questionData['analysis']['analysis']))
                <div class="analysis" data-answer>
                    <div class="analysis-head"><i class="uk-icon-list-alt"></i> 解析</div>
                    <div class="analysis-body">
                        {!! $questionData['analysis']['analysis'] !!}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
