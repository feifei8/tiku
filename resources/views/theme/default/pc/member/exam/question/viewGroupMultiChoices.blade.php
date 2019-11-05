<div class="pb pb-question-view">
    <div class="head">
        <h2>
            第{{$questionItemNumber}}题 多选
        </h2>
    </div>
    <div class="body">

        <div class="question" data-question="multiChoices">
            <div class="question">
                {!! $questionItem['question']['question'] !!}
            </div>
            <div class="option">
                <?php $answers = []; ?>
                @foreach($questionItem['options'] as $index=>$option)
                    <div class="item" data-choice data-choice-is-answer="{{$option['isAnswer']?'true':'false'}}">
                        <div class="choice">{{chr(ord('A')+$index)}}.</div>
                        {!! $option['option'] !!}
                    </div>
                    <?php
                    if($option['isAnswer']){
                        $answers[]=chr(ord('A')+$index);
                    }
                    ?>
                @endforeach
            </div>
            <div class="answer" data-answer>
                <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                <div class="answer-body html-container">
                    @if(empty($answers))
                        <div class="empty">无</div>
                    @else
                        {{join('，',$answers)}}
                    @endif
                </div>
            </div>
            <div class="my-answer">
                @if(\App\Helpers\QuestionExamHelper::isCorrect($questionItem['options'],$paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex]))
                    <div class="uk-alert uk-alert-success">
                        <div class="label">我的答案</div>
                        {{\App\Helpers\QuestionExamHelper::optionToAnswerLabel($paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex])}}
                    </div>
                @else
                    <div class="uk-alert uk-alert-danger">
                        <div class="label">我的答案</div>
                        {{\App\Helpers\QuestionExamHelper::optionToAnswerLabel($paperExamQuestions[$paperQuestionIndex]['answer'][$questionItemIndex])}}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
