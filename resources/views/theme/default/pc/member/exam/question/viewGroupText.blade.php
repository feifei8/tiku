<div class="pb pb-question-view">
    <div class="head">
        <h2>
            第 {{$questionItemNumber}} 题 问答
        </h2>
    </div>
    <div class="body">

        <div class="question" data-question="text">
            <div class="question">
                {!! $questionItem['question']['question'] !!}
            </div>
            <div class="answer" data-answer>
                <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                <div class="answer-body html-container">
                    {!! $questionItem['answer']['answer'] !!}
                </div>
            </div>
            <div class="my-answer">
                @if(\App\Helpers\QuestionExamHelper::isTextCorrect($questionItem['answer']['answer'],$paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex][0]))
                    <div class="uk-alert uk-alert-success">
                        <div class="label">我的答案</div>
                        {!! $paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex][0] !!}
                    </div>
                @else
                    <div class="uk-alert uk-alert-danger">
                        <div class="label">我的答案</div>
                        {!! $paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex][0] !!}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
