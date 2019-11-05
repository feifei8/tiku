@extends('admin::frame')

@section('pageTitle','手动组卷')

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
                    id:'{{$id}}',
                    title:<?php echo json_encode(empty($paper['title'])?'':$paper['title']); ?>,
                    isPublic:<?php echo json_encode(empty($paper['isPublic'])?false:true); ?>,
                    categoryId:<?php echo json_encode(intval($paper['categoryId'])); ?>,
                    totalScore:<?php echo json_encode(empty($paper['totalScore'])?'':$paper['totalScore']); ?>,
                    passScore:<?php echo json_encode(empty($paper['passScore'])?'':$paper['passScore']); ?>,
                    timeLimitEnable:<?php echo json_encode(empty($paper['timeLimitEnable'])?false:true); ?>,
                    timeLimitValue:<?php echo json_encode(empty($paper['timeLimitValue'])?'':$paper['timeLimitValue']); ?>,
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
                    selectQuestion:function () {
                        window.__questionIds = [];
                        window.api.dialog.dialog("{{action('\App\Http\Controllers\Admin\QuestionController@select')}}",{
                            width:'90%',
                            height:'90%',
                            closeCallback: function () {
                                if(window.__questionIds.length==0){
                                    return;
                                }
                                app.loadQuestion(window.__questionIds);
                            }
                        });
                    },
                    calcIndex:function () {
                        for(var i =0,index=1;i<app.questions.length;i++){
                            app.questions[i].index = index;
                            index+=app.questions[i].itemCount;
                        }
                        app.questions = $.extend(true,[],app.questions);
                    },
                    questionMoveUp:function (index) {
                        var item = this.questions.splice(index, 1);
                        this.questions.splice(index - 1, 0, item[0]);
                        this.calcIndex();
                    },
                    questionMoveDown:function (index) {
                        var item = this.questions.splice(index, 1);
                        this.questions.splice(index + 1, 0, item[0]);
                        this.calcIndex();
                    },
                    questionMoveTo:function(index) {
                        var me = this;
                        window.api.dialog.input(function (value) {
                            value = parseInt(value);
                            if(!value){
                                window.api.dialog.tipError('错误的数字');
                                return;
                            }
                            if(value>me.questions.length || value<1){
                                window.api.dialog.tipError('数值范围为1-'+me.questions.length);
                                return;
                            }
                            var item = me.questions.splice(index, 1);
                            me.questions.splice(value-1, 0, item[0]);
                            me.calcIndex();
                        },{
                            label:'跳转到第几题的位置'
                        });
                    },
                    questionDelete:function (index) {
                        this.questions.splice(index,1);
                        this.calcIndex();
                    },
                    save:function (target) {

                        var questions = [];
                        for(var i =0;i<app.questions.length;i++){
                            questions.push({
                                questionId:app.questions[i].id,
                                score:app.questions[i].score
                            });
                        }

                        var data = {};
                        data.title = this.title;
                        data.isPublic = this.isPublic;
                        data.categoryId = this.categoryId;
                        data.totalScore = this.totalScore;
                        data.passScore = this.passScore;
                        data.timeLimitEnable = this.timeLimitEnable;
                        data.timeLimitValue = this.timeLimitValue;
                        data.questions = questions;

                        window.api.dialog.loadingOn();
                        $.post('?',{_id:this.id,data:JSON.stringify(data)},function (res) {
                            window.api.dialog.loadingOff();
                            window.api.base.defaultFormCallback(res,{success:function (res) {
                                if(target=='list'){
                                    window.location.href = "{{action('\App\Http\Controllers\Admin\PaperController@dataList')}}";
                                }else{
                                    window.location.href= "{{action('\App\Http\Controllers\Admin\PaperController@dataAdd')}}";
                                }
                            }});
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

    @if(!$canEdit)
        <div class="uk-alert uk-alert-danger">
            <i class="uk-icon-bell"></i>
            由于该试卷已经有用户参加考试，因此只能修改“试卷标题”和“是否公开试卷”，其他信息的修改将会被忽略。
        </div>
    @endif

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
                                            <i-input v-model="title" style="width:20em;"></i-input>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-1-4">
                                    <div class="line">
                                        <div class="label">
                                            公开试卷
                                        </div>
                                        <div class="field">
                                            <i-switch v-model="isPublic"></i-switch>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-1-4">
                                    <div class="line">
                                        <div class="label">
                                            试卷分类
                                        </div>
                                        <div class="field">
                                            <select v-model="categoryId">
                                                <option value="0">[请选择]</option>
                                                @foreach($paperCategories as $paperCategory)
                                                    <option value="{{$paperCategory['id']}}">{{$paperCategory['name']}}</option>
                                                @endforeach
                                            </select>
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
                                            <i-input v-model="totalScore">
                                                <span slot="prepend">总分</span>
                                            </i-input>
                                        </div>
                                        <div class="uk-width-1-2">

                                            <i-input v-model="passScore">
                                                <span slot="prepend">及格分</span>
                                            </i-input>
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
                                    <div class="uk-grid">
                                        <div class="uk-width-1-2">
                                            <i-switch v-model="timeLimitEnable"></i-switch>
                                        </div>
                                        <div class="uk-width-1-2">
                                            <i-input v-show="timeLimitEnable" v-model="timeLimitValue">
                                                <span slot="prepend">限制时间</span>
                                                <span slot="append">分钟</span>
                                            </i-input>
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
                                                <td colspan="2">
                                                    <div class="uk-alert uk-alert-primary">
                                                        <span style="float:right;">
                                                            <a href="javascript:;" v-on:click="questionMoveUp(questionIndex)" v-show="questionIndex>0" data-uk-tooltip title="向上移动"><i class="uk-icon-arrow-up"></i></a>
                                                            &nbsp;&nbsp;
                                                            <a href="javascript:;" v-on:click="questionMoveDown(questionIndex)" v-show="questionIndex+1<questions.length" data-uk-tooltip title="向下移动"><i class="uk-icon-arrow-down"></i></a>
                                                            &nbsp;&nbsp;
                                                            <a href="javascript:;" v-on:click="questionMoveTo(questionIndex)" v-show="questions.length>1" data-uk-tooltip title="移动到指定位置"><i class="uk-icon-crosshairs"></i></a>
                                                            &nbsp;&nbsp;
                                                            <a data-uk-tooltip title="新窗口编辑题目" target="_blank" :href="'{{action('\App\Http\Controllers\Admin\QuestionController@dataEdit')}}?_id='+questionItem.id"><i class="uk-icon-edit"></i></a>
                                                            &nbsp;
                                                            <a href="javascript:;" v-on:click="questionDelete(questionIndex)" data-uk-tooltip title="删除"><i class="uk-icon-trash"></i></a>
                                                        </span>
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
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <a href="javascript:;" v-on:click="selectQuestion()"><i class="uk-icon-plus"></i> 选择题目</a>
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
                                <button type="button" class="uk-button uk-button-primary" v-on:click="save('continue')">保存并继续添加</button>
                                <button type="button" class="uk-button uk-button-default" v-on:click="save('list')">保存并返回列表</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

@endsection