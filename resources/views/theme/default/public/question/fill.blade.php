 <div class="public-question-view">
    <div class="number">
        @if($number!==null)
            第 {{$number}} 题
            &nbsp;&nbsp;
        @endif
        填空题
    </div>
    <div class="body">
        <div class="question">
            {!! $questionData['question']['question'] !!}
        </div>
        @if(!empty($option['hasAnswer']))
            <div class="answer">
                <div class="label"><i class="uk-icon-gavel"></i> 答案</div>
                <div class="content">
                    @foreach($questionData['answers'] as $answer)
                        <div>
                            {!! $answer['answer'] !!}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if(!empty($option['hasAnalysis']) && !empty($questionData['analysis']['analysis']))
            <div class="analysis">
                <div class="label"><i class="uk-icon-list-alt"></i> 解析</div>
                <div class="content">
                    {!! $questionData['analysis']['analysis'] !!}
                </div>
            </div>
        @endif
    </div>
</div>

