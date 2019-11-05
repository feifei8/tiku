@extends('admin::frameDialog')

@section('headAppend')
    <link rel="stylesheet" href="@assets('assets/vue/iview/iview.css')">
    @parent
    <script src="@assets('assets/vue/iview/iview.js')"></script>
    <style type="text/css">
        .question-filter{}
        .question-filter .basic{padding:10px;}
        .question-filter .basic .field{margin-right:10px;display:inline-block;}
    </style>
@endsection

@section('bodyAppend')
    @parent
    <script>
        $(function(){
            var app = new Vue({
                el: '#questionSelect',
                data: {
                    showExpert:false,

                    questionType:'',
                    questionTitle:'',
                    questionTag:[],

                    pageTotal:0,
                    pageCurrent:0,
                    pageSize:1,
                    questionList:[
                    ],
                    questionSelectIds:[]
                },
                methods:{
                    request:function (page) {
                        page = page || 1;
                        window.api.dialog.loadingOn();
                        $.post('?',{page:page,type:this.questionType,question:this.questionTitle,tags:this.questionTag},function (res) {
                            window.api.dialog.loadingOff();
                            window.api.base.defaultFormCallback(res,{success:function (res) {
                                app.showExpert = false;
                                app.questionList = res.data.list;
                                app.pageTotal = res.data.total;
                                app.pageCurrent = res.data.page;
                                app.pageSize = res.data.pageSize;
                            }});
                        });
                    }
                },
                mounted:function () {
                    this.request();
                    $('.admin-dialog-foot .submit').css({display:'inline-block'}).on('click',function () {
                        window.parent.__questionIds = $.extend([],app.questionSelectIds);
                        $.dialogClose();
                    });
                }
            });
        });
    </script>
@endsection


@section('dialogBody')

    <div id="questionSelect" v-cloak>

        <div class="question-filter">
            <div class="basic">
                <div class="field">
                    <i-select v-model="questionType" style="width:200px" placeholder="请选择题目类型">
                        <i-option value="">所有类型</i-option>
                        @foreach(\App\Types\QuestionType::getList() as $k=>$v)
                            <i-option value="{{$k}}">{{$v}}</i-option>
                        @endforeach
                    </i-select>
                </div>
                <div class="field">
                    <i-input v-model="questionTitle" placeholder="输入题目标题检索"></i-input>
                </div>
                <div class="field">
                    <Checkbox v-model="showExpert">显示高级搜索</Checkbox>
                </div>
                <div class="field">
                    <i-button type="primary" icon="ios-search" v-on:click="request(1)">搜索</i-button>
                </div>
            </div>
            <div class="expert" v-show="showExpert">
                <Checkbox-group v-model="questionTag">
                    <table class="uk-table raw">
                        <tbody>
                        @foreach($groupTags as $groupTag)
                            <tr>
                                <td>{{$groupTag['groupTitle']}}</td>
                                <td>
                                    @foreach($groupTag['groupTags'] as $tag)
                                        <Checkbox label="{{$tag['id']}}">
                                            <span>{{$tag['title']}}</span>
                                        </Checkbox>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </Checkbox-group>
            </div>
        </div>

        <div class="question-list">
            <Checkbox-group v-model="questionSelectIds">
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th width="10">&nbsp;</th>
                            <th>题目</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(questionListItem,questionListIndex) in questionList">
                            <td>
                                <Checkbox :label="questionListItem.id"><span></span></Checkbox>
                            </td>
                            <td>
                                <a :href="questionListItem.url" target="_blank">@{{ questionListItem.question }}</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </Checkbox-group>
        </div>

        <div class="question-page">
            <Page style="margin:0 auto;width:200px;" :current="pageCurrent" :total="pageTotal" :page-size="pageSize" v-on:on-change="request" simple></Page>
        </div>

    </div>


@endsection