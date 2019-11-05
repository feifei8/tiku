<div class="pb pb-question-view">
    <div class="head">
        <h2>
            @if(count($questionItem['answers'])>1)
                第 {{$questionItemNumber}} - {{$questionItemNumber+count($questionItem['answers'])-1}} 第 填空
            @else
                第 {{$questionItemNumber}} 题 填空
            @endif
        </h2>
    </div>
    <div class="body">

        <div class="question" data-question="fill">
            <div class="question">
                {!! $questionItem['question']['question'] !!}
            </div>
            <div class="answer" data-answer>
                <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                <div class="answer-body html-container">
                    @foreach($questionItem['answers'] as $index=>$answer)
                        <p>{!! $answer['answer'] !!}</p>
                    @endforeach
                </div>
            </div>
            <div class="my-answer">
                @foreach($paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex] as $index=>$answer)
                    @if(\App\Helpers\QuestionExamHelper::isTextCorrect($questionItem['answers'][$index]['answer'],$answer))
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
        </div>

    </div>
</div>
