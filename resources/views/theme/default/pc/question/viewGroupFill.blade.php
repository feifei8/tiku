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
            <div class="question html-container">
                {!! $questionItem['question']['question'] !!}
            </div>
            <div class="action">
                <a href="javascript:;" data-answer-show>查看答案</a>
            </div>
            @if($_memberUserId)
                <div class="answer" data-answer>
                    <div class="answer-head"><i class="uk-icon-gavel"></i> 答案</div>
                    <div class="answer-body html-container">
                        @foreach($questionItem['answers'] as $index=>$answer)
                            <p>{!! $answer['answer'] !!}</p>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
