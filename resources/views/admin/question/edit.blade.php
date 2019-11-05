@extends('admin::frame')

@section('pageTitle','题目编辑')

@section('headAppend')
    @parent
    <style type="text/css">
        table.raw tr{background:#FFF !important;}
        table.raw tr td{border:1px solid #CCC !important;padding:10px !important;}
        .widget-tab > .body > .item{padding:10px;}
        [data-html]{min-height:20px;border-radius:3px;padding:2px;border:1px dotted #CCC;background:#FFF;}
        [data-html]:hover{background:#FFFAE8;}
    </style>
@endsection

@section('bodyAppend')
    @parent
    <script>
        $(function(){

            $('.widget-tab > .head > .menu > a').removeClass('cur');
            $('.widget-tab > .body > .item').show();

            var htmlEditorInitValue = null;
            var htmlEditor = window.api.editor('htmlEditor',function () {
                $(htmlEditor.container).click(function(e){
                    e.stopPropagation()
                });
                if(htmlEditorInitValue){
                    htmlEditor.setContent(htmlEditorInitValue);
                    htmlEditorInitValue = null;
                }
            });
            var $htmlEditorBlock = null;
            var checkAndSetHtml = function () {
                if($htmlEditorBlock){
                    var value = htmlEditor.getContent();
                    var key = $htmlEditorBlock.attr('data-key');
                    var index = $htmlEditorBlock.attr('data-index');
                    if(index){
                        window.api.util.set(app,'a'+key+'.'+index,value);
                    }else{
                        app[key] = value;
                    }
                    app.$set(app,key,app[key]);
                    $htmlEditorBlock.html(value);
                    $htmlEditorBlock = null;
                }
            };
            $(document).on('click','[data-html]',function () {
                checkAndSetHtml();
                $htmlEditorBlock = $(this);
                var value = $(this).html();
                $(this).html(htmlEditor.container.parentNode);
                htmlEditorInitValue = value;
                htmlEditor.reset();
                return false;
            });
            $(document).on('click',function () {
                checkAndSetHtml();
                $('#htmlEditorPlaceholder').html(htmlEditor.container.parentNode);
            });


            var app = new window.api.vue({
                el: '#questionEditor',
                data: {
                    id:'{{$id}}',
                    questionType:<?php echo json_encode($data['questionType']); ?>,
                    questionSource:<?php echo json_encode($data['questionSource']); ?>,
                    question: <?php echo json_encode($data['question']); ?>,
                    questionAnalysis:<?php echo json_encode($data['questionAnalysis']); ?>,
                    questionTags:<?php echo json_encode($data['questionTags']); ?>,
                    singleChoiceOption:<?php echo json_encode($data['singleChoiceOption']); ?>,
                    multiChoicesOption:<?php echo json_encode($data['multiChoicesOption']); ?>,
                    trueFalseOption:<?php echo json_encode($data['trueFalseOption']); ?>,
                    fillAnswer:<?php echo json_encode($data['fillAnswer']); ?>,
                    textAnswer:<?php echo json_encode($data['textAnswer']); ?>,
                    items:<?php echo json_encode($data['items']); ?>,
                },
                methods:{
                    singleChoiceOptionChange: function(index,options){
                        if(options[index].isAnswer){
                            options.forEach(function(o,i){
                                if(i!=index){
                                    options[i].isAnswer = false;
                                }
                            })
                        }
                    },
                    setQuestionType:function (type) {
                        this.questionType = type;
                    },
                    add:function(key,index,value){
                        if(index){
                            window.api.util.push(app,'a'+key+'.'+index,value);
                        }else{
                            window.api.util.push(app,'a'+key,value);
                        }
                        app.$set(app,key,app[key]);
                    },
                    remove:function(key,index){
                        window.api.util.remove(app,'a'+key+'.'+index);
                        app.$set(app,key,app[key]);
                    },
                    tagChange:function () {
                        var tags = [];
                        $('[data-tag]:checked').each(function(i,o){
                            tags.push(parseInt($(o).val()));
                        });
                        this.questionTags = tags;
                    },
                    arrayMoveUp:function (items,index) {
                        var item = this.items.splice(index, 1);
                        this.items.splice(index - 1, 0, item[0]);
                    },
                    arrayMoveDown:function (items,index) {
                        var item = this.items.splice(index, 1);
                        this.items.splice(index + 1, 0, item[0]);
                    },
                    save:function (next) {
                        checkAndSetHtml();
                        var data = {};
                        data.next = next;
                        data._id = this.id;
                        data.question = this.question;
                        data.questionType = this.questionType;
                        data.questionSource = this.questionSource;
                        data.questionAnalysis = this.questionAnalysis;
                        data.questionTags = this.questionTags;
                        data.singleChoiceOption = this.singleChoiceOption;
                        data.multiChoicesOption = this.multiChoicesOption;
                        data.trueFalseOption = this.trueFalseOption;
                        data.fillAnswer = this.fillAnswer;
                        data.textAnswer = this.textAnswer;
                        data.items = this.items;
                        window.api.dialog.loadingOn();
                        $.post('?',data,function (res) {
                            window.api.dialog.loadingOff();
                            window.api.base.defaultFormCallback(res);
                        });
                        return false;
                    }
                }
            });

        });
    </script>
@endsection

@section('bodyContent')

    @if($id>0)
        @if($paperQuestion = \Edwin404\Base\Support\ModelHelper::load('paper_question',['questionId'=>$id]))
            <div class="uk-alert uk-alert-danger">
                <?php $paper = \Edwin404\Base\Support\ModelHelper::load('paper',['id'=>$paperQuestion['paperId']]); ?>
                <i class="uk-icon-bell"></i>
                该题目已经加入考卷《{{$paper['title']}}》，修改题目的选项个数、题目类型都会造成考卷不能正常考试，请慎重操作。
            </div>
        @endif
    @endif

    <div class="block admin-form">
        <form action="?" class="uk-form" method="post" onsubmit="return false;">
            <div style="font-size:13px;">
                <table class="uk-table uk-table-radius uk-table-striped" id="questionEditor">
                    <tbody>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">
                                    题目
                                </div>
                                <div class="field">
                                    <div data-html v-html="question" data-key="question"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="field">
                                    <div class="widget-tab widget-tab-secondary" data-disabled>
                                        <div class="head">
                                            <div class="menu">
                                                <a href="javascript:;" v-bind:class="{cur: questionType == {{\App\Types\QuestionType::SINGLE_CHOICE}} }" @click="setQuestionType({{\App\Types\QuestionType::SINGLE_CHOICE}})">单项选择</a>
                                                <a href="javascript:;" v-bind:class="{cur: questionType == {{\App\Types\QuestionType::MULTI_CHOICES}} }" @click="setQuestionType({{\App\Types\QuestionType::MULTI_CHOICES}})">多项选择</a>
                                                <a href="javascript:;" v-bind:class="{cur: questionType == {{\App\Types\QuestionType::TRUE_FALSE}} }" @click="setQuestionType({{\App\Types\QuestionType::TRUE_FALSE}})">判断题</a>
                                                <a href="javascript:;" v-bind:class="{cur: questionType == {{\App\Types\QuestionType::FILL}} }" @click="setQuestionType({{\App\Types\QuestionType::FILL}})">填空题</a>
                                                <a href="javascript:;" v-bind:class="{cur: questionType == {{\App\Types\QuestionType::TEXT}} }" @click="setQuestionType({{\App\Types\QuestionType::TEXT}})">问答题</a>
                                                <a href="javascript:;" v-bind:class="{cur: questionType == {{\App\Types\QuestionType::GROUP}} }" @click="setQuestionType({{\App\Types\QuestionType::GROUP}})">{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,\App\Types\QuestionType::GROUP)}}</a>
                                            </div>
                                        </div>
                                        <div class="body">
                                            <div class="item" v-show="questionType == {{\App\Types\QuestionType::SINGLE_CHOICE}}">
                                                <table class="uk-table raw">
                                                    <tbody>
                                                        <tr>
                                                            <td width="60" class="uk-text-center">是否答案</td>
                                                            <td>选项</td>
                                                            <td width="10"></td>
                                                        </tr>
                                                        <tr v-for="(opt,idx) in singleChoiceOption">
                                                            <td class="uk-text-center">
                                                                <label>
                                                                    <input type="checkbox" name="option" v-model="opt.isAnswer" @change="singleChoiceOptionChange(idx,singleChoiceOption)" value="true" />
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <div data-html v-html="opt.option" data-key="singleChoiceOption" v-bind:data-index="'i'+idx+'.aoption'"></div>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:;" @click="remove('singleChoiceOption','i'+idx)"><i class="uk-icon-remove"></i></a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="uk-text-center">
                                                                <a href="javascript:;" @click="add('singleChoiceOption',null,{isAnswer:false,option:''})"><i class="uk-icon-plus"></i> 增加一个选项</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="item" v-show="questionType == {{\App\Types\QuestionType::MULTI_CHOICES}}">
                                                <table class="uk-table raw">
                                                    <tbody>
                                                    <tr>
                                                        <td width="60" class="uk-text-center">是否答案</td>
                                                        <td>选项</td>
                                                        <td width="10"></td>
                                                    </tr>
                                                    <tr v-for="(opt,idx) in multiChoicesOption">
                                                        <td class="uk-text-center">
                                                            <label>
                                                                <input type="checkbox" name="option" v-model="opt.isAnswer" value="true" />
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="opt.option" data-key="multiChoicesOption" v-bind:data-index="'i'+idx+'.aoption'"></div>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" @click="remove('multiChoicesOption','i'+idx)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="uk-text-center">
                                                            <a href="javascript:;" @click="add('multiChoicesOption',null,{isAnswer:false,option:''})"><i class="uk-icon-plus"></i> 增加一个选项</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="item" v-show="questionType == {{\App\Types\QuestionType::TRUE_FALSE}}">
                                                <table class="uk-table raw">
                                                    <tbody>
                                                    <tr>
                                                        <td width="60" class="uk-text-center">是否答案</td>
                                                        <td>选项</td>
                                                    </tr>
                                                    <tr v-for="(opt,idx) in trueFalseOption">
                                                        <td class="uk-text-center">
                                                            <label>
                                                                <input type="checkbox" value="true" name="trueFalseOption" @change="singleChoiceOptionChange(idx,trueFalseOption)" v-model="opt.isAnswer" />
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div v-html="opt.option"></div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="item" v-show="questionType == {{\App\Types\QuestionType::FILL}}">
                                                <table class="uk-table raw">
                                                    <tbody>
                                                    <tr>
                                                        <td width="5">&nbsp;</td>
                                                        <td>答案</td>
                                                        <td width="5"></td>
                                                    </tr>
                                                    <tr v-for="(ans,idx) in fillAnswer">
                                                        <td>@{{idx+1}}</td>
                                                        <td>
                                                            <div data-html v-html="ans.answer" data-key="fillAnswer" v-bind:data-index="'i'+idx+'.aanswer'"></div>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" @click="remove('fillAnswer','i'+idx)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="uk-text-center">
                                                            <a href="javascript:;" @click="add('fillAnswer',null,{answer:''})"><i class="uk-icon-plus"></i> 增加一个答案</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="item" v-show="questionType == {{\App\Types\QuestionType::TEXT}}">
                                                <table class="uk-table raw">
                                                    <tbody>
                                                    <tr>
                                                        <td>答案</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div data-html v-html="textAnswer[0].answer" data-key="textAnswer" data-index="i0.aanswer"></div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="item" v-show="questionType == {{\App\Types\QuestionType::GROUP}}">

                                                <table class="uk-table raw" v-for="(singleQuestion,singleIndex) in items" style="margin-bottom:20px;">

                                                    <tbody>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div style="float:right;">
                                                                <a href="javascript:;" v-on:click="arrayMoveUp(items,singleIndex)" v-show="singleIndex>0" data-uk-tooltip title="向上移动"><i class="uk-icon-arrow-up"></i></a>
                                                                &nbsp;&nbsp;
                                                                <a href="javascript:;" v-on:click="arrayMoveDown(items,singleIndex)" v-show="singleIndex+1<items.length" data-uk-tooltip title="向下移动"><i class="uk-icon-arrow-down"></i></a>
                                                                &nbsp;&nbsp;
                                                                <a href="javascript:;" style="float:right;" v-on:click="items.splice(singleIndex,1)"><i class="uk-icon-trash"></i></a>
                                                            </div>
                                                            <label>
                                                                <input type="radio" v-bind:name="'items-'+singleIndex+'-type'" v-model="singleQuestion.type" value="{{\App\Types\QuestionType::SINGLE_CHOICE}}" />
                                                                单选
                                                            </label>
                                                            <label>
                                                                <input type="radio" v-bind:name="'items-'+singleIndex+'-type'" v-model="singleQuestion.type" value="{{\App\Types\QuestionType::MULTI_CHOICES}}" />
                                                                多选
                                                            </label>
                                                            <label>
                                                                <input type="radio" v-bind:name="'items-'+singleIndex+'-type'" v-model="singleQuestion.type" value="{{\App\Types\QuestionType::TRUE_FALSE}}" />
                                                                判断题
                                                            </label>
                                                            <label>
                                                                <input type="radio" v-bind:name="'items-'+singleIndex+'-type'" v-model="singleQuestion.type" value="{{\App\Types\QuestionType::FILL}}" />
                                                                填空题
                                                            </label>
                                                            <label>
                                                                <input type="radio" v-bind:name="'items-'+singleIndex+'-type'" v-model="singleQuestion.type" value="{{\App\Types\QuestionType::TEXT}}" />
                                                                问答题
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                    {{--单选--}}
                                                    <tbody v-if="singleQuestion.type=={{\App\Types\QuestionType::SINGLE_CHOICE}}">
                                                    <tr>
                                                        <td width="60" class="uk-text-center">
                                                            问题
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="singleQuestion.question" data-key="items" v-bind:data-index="'i'+singleIndex+'.aquestion'"></div>
                                                        </td>
                                                        <td width="10">
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr v-for="(opt,idx) in singleQuestion.singleChoiceOption">
                                                        <td class="uk-text-center">
                                                            <label>
                                                                <input type="checkbox" v-bind:name="'items_'+singleIndex" @change="singleChoiceOptionChange(idx,singleQuestion.singleChoiceOption)" v-model="opt.isAnswer" value="true" />
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="opt.option" data-key="items" v-bind:data-index="'i'+singleIndex+'.asingleChoiceOption.i'+idx+'.aoption'"></div>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex+'.asingleChoiceOption.i'+idx)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="uk-text-center">
                                                            <a href="javascript:;" @click="add('items','i'+singleIndex+'.asingleChoiceOption',{option:'',isAnswer:false})"><i class="uk-icon-plus"></i> 增加一个选项</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                    {{--多选--}}
                                                    <tbody v-if="singleQuestion.type=={{\App\Types\QuestionType::MULTI_CHOICES}}">
                                                    <tr>
                                                        <td width="60" class="uk-text-center">
                                                            问题
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="singleQuestion.question" data-key="items" v-bind:data-index="'i'+singleIndex+'.aquestion'"></div>
                                                        </td>
                                                        <td width="10">
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr v-for="(opt,idx) in singleQuestion.multiChoicesOption">
                                                        <td class="uk-text-center">
                                                            <label>
                                                                <input type="checkbox" v-bind:name="'items-'+singleIndex" v-model="opt.isAnswer" value="true" />
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="opt.option" data-key="items" v-bind:data-index="'i'+singleIndex+'.amultiChoicesOption.i'+idx+'.aoption'"></div>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex+'.amultiChoicesOption.i'+idx)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="uk-text-center">
                                                            <a href="javascript:;" @click="add('items','i'+singleIndex+'.amultiChoicesOption',{option:'',isAnswer:false})"><i class="uk-icon-plus"></i> 增加一个选项</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                    {{--判断题--}}
                                                    <tbody v-if="singleQuestion.type=={{\App\Types\QuestionType::TRUE_FALSE}}">
                                                    <tr>
                                                        <td width="60" class="uk-text-center">
                                                            问题
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="singleQuestion.question" data-key="items" v-bind:data-index="'i'+singleIndex+'.aquestion'"></div>
                                                        </td>
                                                        <td width="10">
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr v-for="(opt,idx) in singleQuestion.trueFalseOption">
                                                        <td class="uk-text-center">
                                                            <label>
                                                                <input type="checkbox" v-bind:name="'items-'+singleIndex" v-model="opt.isAnswer" @change="singleChoiceOptionChange(idx,singleQuestion.trueFalseOption)" value="true" />
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div v-html="opt.option"></div>
                                                        </td>
                                                        <td>
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                    {{--填空题--}}
                                                    <tbody v-if="singleQuestion.type=={{\App\Types\QuestionType::FILL}}">
                                                    <tr>
                                                        <td width="60" class="uk-text-center">
                                                            问题
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="singleQuestion.question" data-key="items" v-bind:data-index="'i'+singleIndex+'.aquestion'"></div>
                                                        </td>
                                                        <td width="10">
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr v-for="(ans,idx) in singleQuestion.fillAnswer">
                                                        <td>@{{idx+1}}</td>
                                                        <td>
                                                            <div data-html v-html="ans.answer" data-key="items" v-bind:data-index="'i'+singleIndex+'.afillAnswer.i'+idx+'.aanswer'"></div>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex+'.afillAnswer.i'+idx)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="uk-text-center">
                                                            <a href="javascript:;" @click="add('items','i'+singleIndex+'.afillAnswer',{answer:''})"><i class="uk-icon-plus"></i> 增加一个答案</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                    {{--问答题--}}
                                                    <tbody v-if="singleQuestion.type=={{\App\Types\QuestionType::TEXT}}">
                                                    <tr>
                                                        <td width="60" class="uk-text-center">
                                                            问题
                                                        </td>
                                                        <td>
                                                            <div data-html v-html="singleQuestion.question" data-key="items" v-bind:data-index="'i'+singleIndex+'.aquestion'"></div>
                                                        </td>
                                                        <td width="10">
                                                            <a href="javascript:;" @click="remove('items','i'+singleIndex)"><i class="uk-icon-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="uk-text-center">答案</td>
                                                        <td>
                                                            <div data-html v-html="singleQuestion.textAnswer[0].answer" data-key="items" v-bind:data-index="'i'+singleIndex+'.atextAnswer.i0.aanswer'"></div>
                                                        </td>
                                                        <td>
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    </tbody>


                                                </table>

                                                {{--增加题目--}}
                                                <div class="uk-text-center">
                                                    <a href="javascript:;" @click="add('items',null,{question:'',type:'{{\App\Types\QuestionType::SINGLE_CHOICE}}',singleChoiceOption:[{option:'',isAnswer:false}],multiChoicesOption:[{option:'',isAnswer:false}],trueFalseOption:[{option:'正确',isAnswer:false},{option:'错误',isAnswer:false}],fillAnswer:[{answer:''}],textAnswer:[{answer:''}]})"><i class="uk-icon-plus"></i> 增加一个问题</a>
                                                </div>

                                            </div>
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
                                    解析
                                </div>
                                <div class="field">
                                    <div data-html v-html="questionAnalysis" data-key="questionAnalysis"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">
                                    标签
                                </div>
                                <div class="field">
                                    <table class="uk-table raw">
                                        <tbody>
                                            @foreach($groupTags as $groupTag)
                                                <tr>
                                                    <td>{{$groupTag['groupTitle']}}</td>
                                                    <td>
                                                        @foreach($groupTag['groupTags'] as $tag)
                                                            <label>
                                                                <input type="checkbox" data-tag value="{{$tag['id']}}" @change="tagChange()" v-bind:checked="questionTags.indexOf({{$tag['id']}})!=-1" />
                                                                {{$tag['title']}}
                                                            </label>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">
                                    试题来源
                                </div>
                                <div class="field">
                                    <input type="text" v-model="questionSource" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <button type="button" class="uk-button uk-button-primary" @click="save(true)">保存并继续添加</button>
                                <button type="button" class="uk-button uk-button-default" @click="save(false)">保存并返回列表</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <div style="visibility:hidden;" id="htmlEditorPlaceholder">
        <script id="htmlEditor" name="htmlEditor" type="text/plain"></script>
    </div>

@endsection