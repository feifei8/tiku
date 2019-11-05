@if(!empty($paperQuestionNumber))
    <div class="pb pb-question-view-number">
        <div>
            @if($paperQuestionNumberCount>1)
                第 {{$paperQuestionNumber}}-{{$paperQuestionNumber+$paperQuestionNumberCount-1}} 题
            @else
                第 {{$paperQuestionNumber}} 题
            @endif
            &nbsp;&nbsp;
            <i class="uk-icon-server"></i>{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,\App\Types\QuestionType::GROUP)}}
        </div>
    </div>
@endif
<div class="pb pb-question-view">
    @if(empty($paperQuestionNumber))
        <div class="head">
            <h2>
                {{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,\App\Types\QuestionType::GROUP)}}
            </h2>
        </div>
    @endif
    <div class="body">

        <div class="question">
            <div class="question html-container">
                {!! $questionData['question']['question'] !!}
            </div>
        </div>

        <div class="question-items">

            <?php $questionItemNumber = 1; ?>
            @foreach($questionData['items'] as $questionItemIndex=>$questionItem)
                @if($questionItem['question']['type']==\App\Types\QuestionType::SINGLE_CHOICE)
                    @include('theme.default.pc.question.viewGroupSingleChoice')
                @elseif($questionItem['question']['type']==\App\Types\QuestionType::MULTI_CHOICES)
                    @include('theme.default.pc.question.viewGroupMultiChoices')
                @elseif($questionItem['question']['type']==\App\Types\QuestionType::TRUE_FALSE)
                    @include('theme.default.pc.question.viewGroupTrueFalse')
                @elseif($questionItem['question']['type']==\App\Types\QuestionType::FILL)
                    @include('theme.default.pc.question.viewGroupFill')
                    <?php $questionItemNumber+=count($questionItem['answers'])-1; ?>
                @elseif($questionItem['question']['type']==\App\Types\QuestionType::TEXT)
                    @include('theme.default.pc.question.viewGroupText')
                @endif
                <?php $questionItemNumber++; ?>
            @endforeach

        </div>

    </div>
</div>

<div class="pb pb-question-view" data-group-analysis style="display:none;">
    <div class="head">
        <h2>解析</h2>
    </div>
    <div class="body">
        <div class="analysis html-container">
            @if($questionData['analysis']['analysis'])
                {!! $questionData['analysis']['analysis'] !!}
            @else
                <div class="empty">
                    无
                </div>
            @endif
        </div>
    </div>
</div>