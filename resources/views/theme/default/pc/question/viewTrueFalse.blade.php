@if(!empty($paperQuestionNumber))
    <div class="pb pb-question-view-number">
        <div>
            @if($paperQuestionNumberCount>1)
                第 {{$paperQuestionNumber}}-{{$paperQuestionNumber+$paperQuestionNumberCount-1}} 题
            @else
                第 {{$paperQuestionNumber}} 题
            @endif
            &nbsp;&nbsp;
            <i class="uk-icon-toggle-off"></i>判断题
        </div>
    </div>
@endif
<div class="pb pb-question-view">
    @if(empty($paperQuestionNumber))
        <div class="head">
            <h2>
                判断题
            </h2>
        </div>
    @endif
    <div class="body">

        <div class="question" data-question="trueFalse" data-alias="{{$questionData['question']['alias']}}">
            <div class="question html-container">
                {!! $questionData['question']['question'] !!}
            </div>
            <div class="option">
                <?php $answers = []; ?>
                @foreach($questionData['options'] as $index=>$option)
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
            @if($_memberUserId)
                <div class="answer-result-correct" data-answer-correct>
                    <div class="result"><i class="uk-icon-check-circle"></i> 回答正确</div>
                </div>
                <div class="answer-result-incorrect" data-answer-incorrect>
                    <div class="result"><i class="uk-icon-warning"></i> 回答错误</div>
                </div>
                <div class="answer" data-answer>
                    <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                    <div class="answer-body html-container">
                        {{join('，',$answers)}}
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