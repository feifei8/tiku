@extends('admin::frame')

@section('pageTitle','阅卷')

@section('headAppend')
    <link rel="stylesheet" href="@assets('assets/vue/iview/iview.css')">
    @parent
    <script src="@assets('assets/vue/iview/iview.js')"></script>
    <style type="text/css">
        table.raw tr{background:#FFF !important;}
        table.raw tr td{border:none !important;padding:10px !important;}
    </style>
@endsection

@section('bodyAppend')
    @parent
    <script>
        $(function(){
            var app = new Vue({
                el: '#paperEditor',
                data: {
                    questions:[],
                    paperExamQuestions:<?php echo json_encode($paperExamQuestions); ?>
                },
                methods:{
                    loadQuestion:function (ids,cb) {
                        window.api.dialog.loadingOn();
                        $.post("{{action('\App\Http\Controllers\Admin\QuestionController@preview')}}",{ids:ids},function (res) {
                            window.api.dialog.loadingOff();
                            for(var i=0;i<res.data.list.length;i++){
                                app.questions.push(res.data.list[i]);
                            }
                            app.calcIndex();
                            cb && cb();
                        })
                    },
                    calcIndex:function () {
                        for(var i =0,index=1;i<app.questions.length;i++){
                            app.questions[i].index = index;
                            index+=app.questions[i].itemCount;
                        }
                        app.questions = $.extend(true,[],app.questions);
                    },
                    save:function () {
                        window.api.dialog.loadingOn();
                        $.post('?',{
                            _id:{{$id}},
                            data:JSON.stringify(this.paperExamQuestions)
                        },function (res) {
                            window.api.dialog.loadingOff();
                            window.api.base.defaultFormCallback(res) ;
                        });
                    }
                },
                mounted:function () {
                    var me = this;
                    @if(!empty($paperQuestions))
                        var ids = [];
                        @foreach($paperQuestions as $paperQuestion)
                            ids.push({{$paperQuestion['questionId']}});
                        @endforeach
                        this.loadQuestion(ids,function () {
                            @foreach($paperQuestions as $index=>$paperQuestion)
                                @foreach($paperQuestion['score'] as $scoreIndex=>$score)
                                    me.questions[{{$index}}].score[{{$scoreIndex}}] = {{$score}};
                                @endforeach
                            @endforeach
                        });
                    @endif
                }
            });
        });
    </script>
@endsection

@section('bodyContent')



    <div class="block admin-form" id="paperEditor" v-cloak>
        <div class="uk-form">

            <div style="font-size:13px;">
                <table class="uk-table uk-table-radius uk-table-striped">
                    <tbody>
                    <tr>
                        <td>
                            <div class="uk-grid">
                                <div class="uk-width-1-1">
                                    <div class="line">
                                        <div class="label">
                                            试卷名称
                                        </div>
                                        <div class="field">
                                            {{$paper['title']}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">
                                    题目列表
                                </div>
                                <div class="field">
                                    <table class="raw" style="margin:0 -10px;">
                                        <tbody v-for="(questionItem,questionIndex) in questions">
                                            <tr>
                                                <td colspan="4">
                                                    <div class="uk-alert uk-alert-primary">
                                                        <span v-if="questionItem.itemCount==1">第 @{{questionItem.index}} 题</span>
                                                        <span v-if="questionItem.itemCount>1">第 @{{questionItem.index}} - @{{questionItem.index+questionItem.itemCount-1}} 题</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div v-html="questionItem.html"></div>
                                                </td>
                                                <td width="200">
                                                    <div>
                                                        <div v-if="questionItem.questionData.question.type=={{\App\Types\QuestionType::SINGLE_CHOICE}}">
                                                            <div class="uk-alert uk-alert-danger">用户答案</div>
                                                            <div class="uk-alert uk-alert-warning">
                                                                @{{ String.fromCharCode('A'.charCodeAt()+paperExamQuestions[questionIndex].answer[0]) }}
                                                            </div>
                                                        </div>
                                                        <div v-if="questionItem.questionData.question.type=={{\App\Types\QuestionType::MULTI_CHOICES}}">
                                                            <div class="uk-alert uk-alert-danger">用户答案</div>
                                                            <div class="uk-alert uk-alert-warning">
                                                                <span v-for="ans in paperExamQuestions[questionIndex].answer">
                                                                    @{{ String.fromCharCode('A'.charCodeAt()+ans) }}
                                                                    &nbsp;
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div v-if="questionItem.questionData.question.type=={{\App\Types\QuestionType::TRUE_FALSE}}">
                                                            <div class="uk-alert uk-alert-danger">用户答案</div>
                                                            <div class="uk-alert uk-alert-warning">
                                                                <div v-if="paperExamQuestions[questionIndex].answer[0]==0">正确</div>
                                                                <div v-if="paperExamQuestions[questionIndex].answer[0]==1">错误</div>
                                                            </div>
                                                        </div>
                                                        <div v-if="questionItem.questionData.question.type=={{\App\Types\QuestionType::FILL}}">
                                                            <div class="uk-alert uk-alert-danger">用户答案</div>
                                                            <div>
                                                                <div v-for="(ans,ansIndex) in paperExamQuestions[questionIndex].answer">
                                                                    <div class="uk-alert uk-alert-warning" v-html="ans"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div v-if="questionItem.questionData.question.type=={{\App\Types\QuestionType::TEXT}}">
                                                            <div class="uk-alert uk-alert-danger">用户答案</div>
                                                            <div>
                                                                <div class="uk-alert uk-alert-warning" v-html="paperExamQuestions[questionIndex].answer[0]"></div>
                                                            </div>
                                                        </div>
                                                        <div v-if="questionItem.questionData.question.type=={{\App\Types\QuestionType::GROUP}}">
                                                            <div class="uk-alert uk-alert-danger">用户答案</div>
                                                            <div v-for="(ansGroup,ansGroupIndex) in paperExamQuestions[questionIndex].answer">
                                                                <div v-if="questionItem.questionData.items[ansGroupIndex].question.type=={{\App\Types\QuestionType::SINGLE_CHOICE}}">
                                                                    <div class="uk-alert uk-alert-warning">
                                                                        @{{ String.fromCharCode('A'.charCodeAt()+ansGroup[0]) }}
                                                                    </div>
                                                                </div>
                                                                <div v-if="questionItem.questionData.items[ansGroupIndex].question.type=={{\App\Types\QuestionType::MULTI_CHOICES}}">
                                                                    <div class="uk-alert uk-alert-warning">
                                                                        <span v-for="ans in ansGroup">
                                                                            @{{ String.fromCharCode('A'.charCodeAt()+ans) }}
                                                                            &nbsp;
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div v-if="questionItem.questionData.items[ansGroupIndex].question.type=={{\App\Types\QuestionType::TRUE_FALSE}}">
                                                                    <div class="uk-alert uk-alert-warning">
                                                                        <div v-if="ansGroup[0]==0">正确</div>
                                                                        <div v-if="ansGroup[0]==1">错误</div>
                                                                    </div>
                                                                </div>
                                                                <div v-if="questionItem.questionData.items[ansGroupIndex].question.type=={{\App\Types\QuestionType::FILL}}">
                                                                    <div class="uk-alert uk-alert-warning" v-for="ans in ansGroup">
                                                                        <div v-html="ans"></div>
                                                                    </div>
                                                                </div>
                                                                <div v-if="questionItem.questionData.items[ansGroupIndex].question.type=={{\App\Types\QuestionType::TEXT}}">
                                                                    <div class="uk-alert uk-alert-warning" v-html="ansGroup[0]"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td width="120">
                                                    <div v-for="(questionScoreItem,questionScoreIndex) in questionItem.score">
                                                        <i-input v-model="questionItem.score[questionScoreIndex]" :disabled="true">
                                                            <span slot="prepend">分值</span>
                                                        </i-input>
                                                    </div>
                                                </td>
                                                <td width="120">
                                                    <div v-for="(questionScoreItem,questionScoreIndex) in paperExamQuestions[questionIndex].score">
                                                        <i-input v-model="paperExamQuestions[questionIndex].score[questionScoreIndex]">
                                                            <span slot="prepend">得分</span>
                                                        </i-input>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <button type="button" class="uk-button uk-button-primary" v-on:click="save()">保存</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

@endsection