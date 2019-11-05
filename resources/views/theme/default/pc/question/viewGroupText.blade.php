<div class="pb pb-question-view">
    <div class="head">
        <h2>
            第 {{$questionItemNumber}} 题 问答
        </h2>
    </div>
    <div class="body">

        <div class="question" data-question="text">
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
                        {!! $questionItem['answer']['answer'] !!}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
