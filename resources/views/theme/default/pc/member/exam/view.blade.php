@extends('theme.default.pc.frame')

@section('pageTitleMain',htmlspecialchars($paper['title']))

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/member/{{$_memberUser['id']}}">我的中心</a></li>
                <li><a href="/member/exam">我的考试</a></li>
                <li class="uk-active"><span>{{$paper['title']}}</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-2">

                <div class="pb pb-paper-view">
                    <div class="head">
                        <h2>
                            我的成绩
                        </h2>
                    </div>
                    <div class="body">

                        <div class="attr">
                            <div class="line">
                                <i class="uk-icon-check-circle-o"></i> 分数：{{$paperExam['score']}}
                            </div>
                            <div class="line">
                                @if($paper['timeLimitEnable'])
                                    <i class="uk-icon-clock-o"></i> 时间：{{$paperExam['created_at']}}
                                @else
                                    <i class="uk-icon-clock-o"></i> 时间：不限时
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="uk-width-1-2">

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
                        </div>

                    </div>
                </div>

            </div>

            <div class="uk-width-1-1">

                <div style="height:20px;"></div>

                <div id="memberExamQuestionList">
                    @if(!empty($paperQuestions))
                        <?php $paperQuestionNumber = 1; ?>
                        @foreach($paperQuestions as $paperQuestionIndex=>$paperQuestion)
                            <?php $questionData = &$paperQuestion['_questionData']; ?>
                            <?php $paperQuestionNumberCount = count($paperQuestion['score']); ?>
                            @if($questionData['question']['type']==\App\Types\QuestionType::SINGLE_CHOICE)
                                @include('theme.default.pc.member.exam.question.viewSingleChoice')
                            @elseif($questionData['question']['type']==\App\Types\QuestionType::MULTI_CHOICES)
                                @include('theme.default.pc.member.exam.question.viewMultiChoices')
                            @elseif($questionData['question']['type']==\App\Types\QuestionType::TRUE_FALSE)
                                @include('theme.default.pc.member.exam.question.viewTrueFalse')
                            @elseif($questionData['question']['type']==\App\Types\QuestionType::FILL)
                                @include('theme.default.pc.member.exam.question.viewFill')
                            @elseif($questionData['question']['type']==\App\Types\QuestionType::TEXT)
                                @include('theme.default.pc.member.exam.question.viewText')
                            @elseif($questionData['question']['type']==\App\Types\QuestionType::GROUP)
                                @include('theme.default.pc.member.exam.question.viewGroup')
                            @endif
                            <?php $paperQuestionNumber += $paperQuestionNumberCount; ?>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>

    </div>


@endsection