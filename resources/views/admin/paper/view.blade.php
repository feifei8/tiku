@extends('admin::frame')

@section('pageTitle','试卷预览')

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
                    questions:[]
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
                                <div class="uk-width-1-2">
                                    <div class="line">
                                        <div class="label">
                                            试卷名称
                                        </div>
                                        <div class="field">
                                            {{$paper['title']}}
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-1-2">
                                    <div class="line">
                                        <div class="label">
                                            公开试卷
                                        </div>
                                        <div class="field">
                                            {{$paper['isPublic']?'是':'否'}}
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
                                    分数设置
                                </div>
                                <div class="field">
                                    <div class="uk-grid">
                                        <div class="uk-width-1-2">
                                            总分 {{$paper['totalScore']}}
                                        </div>
                                        <div class="uk-width-1-2">
                                            及格分 {{$paper['passScore']}}
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
                                    答题时间限制
                                </div>
                                <div class="field">
                                    @if($paper['timeLimitEnable'])
                                        显示
                                        {{$paper['timeLimitValue']}}分钟
                                    @else
                                        不限制
                                    @endif
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
                                                <td colspan="2">
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
                                                <td width="120">
                                                    <div v-for="(questionScoreItem,questionScoreIndex) in questionItem.score">
                                                        <i-input v-model="questionItem.score[questionScoreIndex]">
                                                            <span slot="prepend">分值</span>
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
                    </tbody>
                </table>
            </div>
        </form>
    </div>

@endsection