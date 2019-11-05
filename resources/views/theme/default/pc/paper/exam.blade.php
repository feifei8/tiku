@extends('theme.default.pc.frame')

@section('pageTitleMain','考试'.htmlspecialchars($paper['title']))

@section('bodyScript')
    <script>
        var __app = {
            examStartUrl:"/paper/exam_start/{{$paper['alias']}}",
            examSubmitUrl:"/paper/exam_submit/{{$paper['alias']}}",
            examSaveUrl:"/paper/exam_save/{{$paper['alias']}}",
            questionType:{
                SINGLE_CHOICE:{{\App\Types\QuestionType::SINGLE_CHOICE}},
                MULTI_CHOICES:{{\App\Types\QuestionType::MULTI_CHOICES}},
                TRUE_FALSE:{{\App\Types\QuestionType::TRUE_FALSE}},
                FILL:{{\App\Types\QuestionType::FILL}},
                TEXT:{{\App\Types\QuestionType::TEXT}},
                GROUP:{{\App\Types\QuestionType::GROUP}}
            }
        };
    </script>
    <script src="@assets('assets/main/default/exam.js')"></script>
@endsection

@section('bodyContent')

    <div class="main-container" id="paperExam" v-cloak>

        <div class="uk-modal" id="paperExamDialog">
            <div class="uk-modal-dialog" style="width:400px;">
                <div class="uk-modal-header"><i class="uk-icon-bell"></i> 考试信息确认</div>
                <div>
                    <div class="pb pb-paper-exam-dialog" style="margin:-10px -20px;">
                        <div class="body">
                            <div class="title">
                                {{$paper['title']}}
                            </div>
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
                </div>
                <div class="uk-modal-footer uk-text-center">
                    <a href="javascript:;" v-on:click="start()" class="uk-button uk-button-large uk-button-danger">开始考试</a>
                    <a href="javascript:;" onclick="window.history.go(-1);" class="uk-button uk-button-large">取消考试</a>
                </div>
            </div>
        </div>

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/paper">试卷</a></li>
                <li><a href="/paper">考试</a></li>
                <li class="uk-active"><span>{{$paper['title']}}</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-3-4">

                <div class="pb pb-paper-exam-title">
                    <div class="body">
                        <div class="title">
                            {{$paper['title']}}
                        </div>
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

                <div v-for="(question,index) in paperQuestionList" class="uk-form">

                    {{--单选--}}
                    <div v-if="question.question.type=={{\App\Types\QuestionType::SINGLE_CHOICE}}">
                        <div class="pb pb-question-exam-number">
                            <div>
                                <span>
                                    第 @{{ question.questionNumber }} 题
                                    <a v-bind:id="'examQ'+question.questionNumber"></a>
                                </span>
                                &nbsp;&nbsp;
                                <i class="uk-icon-dot-circle-o"></i>单选题
                            </div>
                        </div>
                        <div class="pb pb-question-exam">
                            <div class="body">
                                <div class="question" v-html="question.question.question"></div>
                                <div class="option">
                                    <div class="item" v-for="(optionItem,optionIndex) in question.options" v-on:click="singleChoiceClick(paperQuestionList[index],optionIndex)" v-bind:class="{selected:optionItem.isAnswer}">
                                        <div class="choice">@{{ String.fromCharCode('A'.charCodeAt()+optionIndex) }}.</div>
                                        <div v-html="optionItem.option"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--多选--}}
                    <div v-if="question.question.type=={{\App\Types\QuestionType::MULTI_CHOICES}}">
                        <div class="pb pb-question-exam-number">
                            <div>
                                <span>
                                    第 @{{ question.questionNumber }} 题
                                    <a v-bind:id="'examQ'+question.questionNumber"></a>
                                </span>
                                &nbsp;&nbsp;
                                <i class="uk-icon-check-circle"></i>多选题
                            </div>
                        </div>
                        <div class="pb pb-question-exam">
                            <div class="body">
                                <div class="question" v-html="question.question.question"></div>
                                <div class="option">
                                    <div class="item" v-for="(optionItem,optionIndex) in question.options" v-on:click="multiChoicesClick(paperQuestionList[index],optionIndex)" v-bind:class="{selected:optionItem.isAnswer}">
                                        <div class="choice">@{{ String.fromCharCode('A'.charCodeAt()+optionIndex) }}.</div>
                                        <div v-html="optionItem.option"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--判断题--}}
                    <div v-if="question.question.type=={{\App\Types\QuestionType::TRUE_FALSE}}">
                        <div class="pb pb-question-exam-number">
                            <div>
                                <span>
                                    第 @{{ question.questionNumber }} 题
                                    <a v-bind:id="'examQ'+question.questionNumber"></a>
                                </span>
                                &nbsp;&nbsp;
                                <i class="uk-icon-toggle-off"></i>判断题
                            </div>
                        </div>
                        <div class="pb pb-question-exam">
                            <div class="body">
                                <div class="question" v-html="question.question.question"></div>
                                <div class="option">
                                    <div class="item" v-for="(optionItem,optionIndex) in question.options" v-on:click="trueFalseClick(paperQuestionList[index],optionIndex)" v-bind:class="{selected:optionItem.isAnswer}">
                                        <div class="choice">@{{ String.fromCharCode('A'.charCodeAt()+optionIndex) }}.</div>
                                        <div v-html="optionItem.option"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--填空题--}}
                    <div v-if="question.question.type=={{\App\Types\QuestionType::FILL}}">
                        <div class="pb pb-question-exam-number">
                            <div>
                                <span v-if="question.questionCount>1">
                                    第 @{{ question.questionNumber }} - @{{ question.questionNumber + question.questionCount - 1 }} 题
                                </span>
                                <span v-if="question.questionCount==1">
                                    第 @{{ question.questionNumber }} 题
                                </span>
                                <a v-for="n in question.questionCount" v-bind:id="'examQ'+(question.questionNumber+n-1)"></a>
                                &nbsp;&nbsp;
                                <i class="uk-icon-pencil"></i>填空题
                            </div>
                        </div>
                        <div class="pb pb-question-exam">
                            <div class="body">
                                <div class="question" v-html="question.question.question"></div>
                                <div class="answer">
                                    <div class="line" v-for="(answerItem,answerIndex) in question.answers">
                                        <div class="answer-head">空@{{ answerIndex+1 }}</div>
                                        <div class="answer-body">
                                            <input type="text" class="uk-width-1-1" v-model="question.answers[answerIndex].answer" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--问答题--}}
                    <div v-if="question.question.type=={{\App\Types\QuestionType::TEXT}}">
                        <div class="pb pb-question-exam-number">
                            <div>
                                <span>
                                    第 @{{ question.questionNumber }} 题
                                    <a v-bind:id="'examQ'+question.questionNumber"></a>
                                </span>
                                &nbsp;&nbsp;
                                <i class="uk-icon-comment-o"></i>问答题
                            </div>
                        </div>
                        <div class="pb pb-question-exam">
                            <div class="body">
                                <div class="question" v-html="question.question.question"></div>
                                <div class="answer">
                                    <div class="line">
                                        <div class="answer-head">回答</div>
                                        <div class="answer-body">
                                            <textarea rows="3" class="uk-width-1-1" v-model="question.answer.answer"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--多题目--}}
                    <div v-if="question.question.type=={{\App\Types\QuestionType::GROUP}}">
                        <div class="pb pb-question-exam-number">
                            <div>
                                <span v-if="question.questionCount>1">
                                    第 @{{ question.questionNumber }} - @{{ question.questionNumber + question.questionCount - 1 }} 题
                                </span>
                                <span v-if="question.questionCount==1">
                                    第 @{{ question.questionNumber }} 题
                                </span>
                                &nbsp;&nbsp;
                                <i class="uk-icon-list"></i>多题目
                            </div>
                        </div>
                        <div class="pb pb-question-exam">
                            <div class="body">
                                <div class="question" v-html="question.question.question"></div>
                                <div class="question-items">

                                    <div v-for="(questionItemItem,questionItemIndex) in question.items">

                                        {{--单选--}}
                                        <div v-if="questionItemItem.question.type=={{\App\Types\QuestionType::SINGLE_CHOICE}}">
                                            <a v-bind:id="'examQ'+(question.questionNumber+questionItemItem.itemNumber-1)"></a>
                                            <div class="pb pb-question-exam">
                                                <div class="head">
                                                    <h2>第 @{{ question.questionNumber+questionItemItem.itemNumber-1 }} 题 单选</h2>
                                                </div>
                                                <div class="body">
                                                    <div class="question" v-html="questionItemItem.question.question"></div>
                                                    <div class="option">
                                                        <div class="item" v-for="(optionItem,optionIndex) in questionItemItem.options" v-on:click="singleChoiceClick(question.items[questionItemIndex],optionIndex)" v-bind:class="{selected:optionItem.isAnswer}">
                                                            <div class="choice">@{{ String.fromCharCode('A'.charCodeAt()+optionIndex) }}.</div>
                                                            <div v-html="optionItem.option"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--多选--}}
                                        <div v-if="questionItemItem.question.type=={{\App\Types\QuestionType::MULTI_CHOICES}}">
                                            <a v-bind:id="'examQ'+(question.questionNumber+questionItemItem.itemNumber-1)"></a>
                                            <div class="pb pb-question-exam">
                                                <div class="head">
                                                    <h2>第 @{{ question.questionNumber+questionItemItem.itemNumber-1 }} 题 多选</h2>
                                                </div>
                                                <div class="body">
                                                    <div class="question" v-html="questionItemItem.question.question"></div>
                                                    <div class="option">
                                                        <div class="item" v-for="(optionItem,optionIndex) in questionItemItem.options" v-on:click="multiChoicesClick(question.items[questionItemIndex],optionIndex)" v-bind:class="{selected:optionItem.isAnswer}">
                                                            <div class="choice">@{{ String.fromCharCode('A'.charCodeAt()+optionIndex) }}.</div>
                                                            <div v-html="optionItem.option"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--判断题--}}
                                        <div v-if="questionItemItem.question.type=={{\App\Types\QuestionType::TRUE_FALSE}}">
                                            <a v-bind:id="'examQ'+(question.questionNumber+questionItemItem.itemNumber-1)"></a>
                                            <div class="pb pb-question-exam">
                                                <div class="head">
                                                    <h2>第 @{{ question.questionNumber+questionItemItem.itemNumber-1 }} 题 判断</h2>
                                                </div>
                                                <div class="body">
                                                    <div class="question" v-html="questionItemItem.question.question"></div>
                                                    <div class="option">
                                                        <div class="item" v-for="(optionItem,optionIndex) in questionItemItem.options" v-on:click="trueFalseClick(question.items[questionItemIndex],optionIndex)" v-bind:class="{selected:optionItem.isAnswer}">
                                                            <div class="choice">@{{ String.fromCharCode('A'.charCodeAt()+optionIndex) }}.</div>
                                                            <div v-html="optionItem.option"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--填空题--}}
                                        <div v-if="questionItemItem.question.type=={{\App\Types\QuestionType::FILL}}">
                                            <a v-for="n in questionItemItem.itemCount" v-bind:id="'examQ'+(question.questionNumber+questionItemItem.itemNumber+n-1-1)"></a>
                                            <div class="pb pb-question-exam">
                                                <div class="head">
                                                    <h2 v-if="questionItemItem.itemCount==1">第 @{{ question.questionNumber+questionItemItem.itemNumber-1 }} 题 填空</h2>
                                                    <h2 v-if="questionItemItem.itemCount>1">第 @{{ question.questionNumber+questionItemItem.itemNumber-1 }} - @{{ question.questionNumber+questionItemItem.itemNumber-1+questionItemItem.itemCount-1 }} 题 填空</h2>
                                                </div>
                                                <div class="body">
                                                    <div class="question" v-html="questionItemItem.question.question"></div>
                                                    <div class="answer">
                                                        <div class="line" v-for="(answerItem,answerIndex) in questionItemItem.answers">
                                                            <div class="answer-head">空@{{ answerIndex+1 }}</div>
                                                            <div class="answer-body">
                                                                <input type="text" class="uk-width-1-1" v-model="questionItemItem.answers[answerIndex].answer" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--问答题--}}
                                        <div v-if="questionItemItem.question.type=={{\App\Types\QuestionType::TEXT}}">
                                            <a v-bind:id="'examQ'+(question.questionNumber+questionItemItem.itemNumber-1)"></a>
                                            <div class="pb pb-question-exam">
                                                <div class="head">
                                                    <h2>第 @{{ question.questionNumber+questionItemItem.itemNumber-1 }} 题 问答</h2>
                                                </div>
                                                <div class="body">
                                                    <div class="question" v-html="questionItemItem.question.question"></div>
                                                    <div class="answer">
                                                        <div class="line">
                                                            <div class="answer-head">回答</div>
                                                            <div class="answer-body">
                                                                <textarea rows="3" class="uk-width-1-1" v-model="questionItemItem.answer.answer"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="uk-width-1-4">

                <div data-uk-sticky>

                    <div class="pb pb-paper-exam-panel" >
                        <div class="body">

                            <div class="time">
                                <div class="label">
                                    <i class="uk-icon-clock-o"></i> 剩余时间
                                </div>
                                <div class="value">
                                    @{{endTimeString}}
                                </div>
                            </div>

                            <div class="questions">
                                <div class="label">
                                    <i class="uk-icon-list-alt"></i>
                                    答题卡
                                </div>
                                <div class="list">
                                    @for($i=1;$i<=$paper['questionCount'];$i++)
                                        <a href="javascript:;" v-bind:class="{filled:isFilled( {{$i}} )}" v-on:click="jumpQuestion( {{$i}} )">{{$i}}</a>
                                    @endfor
                                </div>
                            </div>

                            <div class="action">
                                <a href="javascript:;" class="submit" v-on:click="submit()">交卷</a>
                            </div>

                        </div>
                    </div>


                    <div class="pb pb-paper-exam-summary">
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

                </div>

            </div>
        </div>

    </div>


@endsection