@extends('theme.default.pc.frame')

@section('pageTitleMain',htmlspecialchars($paper['title']))

@section('bodyScript')
    @parent
    <script>
        var __app = {
            isLogin:{{$_memberUserId?'true':'false'}},
            loginRedirect:'{{urlencode(\Edwin404\Base\Support\RequestHelper::currentPageUrl())}}'
        };
    </script>
    <script src="@assets('assets/main/js/question.js')"></script>
@endsection

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/paper">试卷</a></li>
                <li class="uk-active"><span>{{$paper['title']}}</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-1">

                <div class="pb pb-paper-view">
                    <div class="head">
                        <h2>
                            {{$paper['title']}}
                        </h2>
                    </div>
                    <div class="body">

                        <div class="attr">
                            <div class="line">
                                <i class="uk-icon-certificate"></i> 题目总数：{{$paper['questionCount']}}
                            </div>
                            <div class="line">
                                <i class="uk-icon-check-circle-o"></i> 总分数：{{$paper['totalScore']}}
                            </div>
                            <div class="line">
                                @if($paper['timeLimitEnable'])
                                    <i class="uk-icon-clock-o"></i> 时间：{{$paper['timeLimitValue']}}分钟
                                @else
                                    <i class="uk-icon-clock-o"></i> 时间：不限时
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                @if(!empty($paperQuestions))
                    <?php $questionShowAnswer = !empty($_memberUserId); ?>
                    <?php $paperQuestionNumber = 1; ?>
                    @foreach($paperQuestions as $paperQuestionIndex=>$paperQuestion)
                        <?php $questionData = &$paperQuestion['_questionData']; ?>
                        <?php $paperQuestionNumberCount = count($paperQuestion['score']); ?>
                        @if($questionData['question']['type']==\App\Types\QuestionType::SINGLE_CHOICE)
                            @include('theme.default.pc.question.viewSingleChoice')
                        @elseif($questionData['question']['type']==\App\Types\QuestionType::MULTI_CHOICES)
                            @include('theme.default.pc.question.viewMultiChoices')
                        @elseif($questionData['question']['type']==\App\Types\QuestionType::TRUE_FALSE)
                            @include('theme.default.pc.question.viewTrueFalse')
                        @elseif($questionData['question']['type']==\App\Types\QuestionType::FILL)
                            @include('theme.default.pc.question.viewFill')
                        @elseif($questionData['question']['type']==\App\Types\QuestionType::TEXT)
                            @include('theme.default.pc.question.viewText')
                        @elseif($questionData['question']['type']==\App\Types\QuestionType::GROUP)
                            @include('theme.default.pc.question.viewGroup')
                        @endif
                        <?php $paperQuestionNumber += $paperQuestionNumberCount; ?>
                    @endforeach
                @endif

            </div>
        </div>

    </div>


@endsection