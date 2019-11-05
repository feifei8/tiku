<div style="border:1px solid #CCC;padding:10px;border-radius:3px;line-height:2em;">
    <div style="padding:10px 0;">
        <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
            {{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$question['type'])}}
        </div>
        <div style="padding:10px 0;">
            <div>
                {!! $question['question'] !!}
            </div>
            @if($question['type']==\App\Types\QuestionType::SINGLE_CHOICE || $question['type']==\App\Types\QuestionType::MULTI_CHOICES)
                <div>
                    <?php $answers = []; ?>
                    @foreach($options as $index=>$option)
                        <div style="padding:0 0 0 20px;">
                            <div style="float:left;margin:0 0 0 -20px;">{{chr(ord('A')+$index)}}.</div>
                            {!! $option['option'] !!}
                        </div>
                        <?php
                        if($option['isAnswer']){
                            $answers[]=chr(ord('A')+$index);
                        }
                        ?>
                    @endforeach
                </div>
                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                    答案:
                    {{join('，',$answers)}}
                </div>
            @endif
            @if($question['type']==\App\Types\QuestionType::TRUE_FALSE)
                <div>
                    <?php $answer = null; ?>
                    @foreach($options as $index=>$option)
                        <div style="padding:0 0 0 20px;">
                            {!! $option['option'] !!}
                        </div>
                        <?php
                        if($option['isAnswer']){
                            $answer = $option['option'];
                        }
                        ?>
                    @endforeach
                </div>
                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                    答案:
                    {{$answer}}
                </div>
            @endif
            @if($question['type']==\App\Types\QuestionType::FILL)
                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                    答案
                </div>
                <div style="padding:10px 0;">
                    @foreach($answers as $index=>$answer)
                        <div style="padding:0 0 0 20px;">
                            <div style="float:left;margin:0 0 0 -20px;">{{$index+1}}.</div>
                            {!! $answer['answer'] !!}
                        </div>
                    @endforeach
                </div>
            @endif
            @if($question['type']==\App\Types\QuestionType::TEXT)
                <div style="padding:10px 0;">
                    <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                        答案
                    </div>
                    <div style="padding:10px 0;">
                        {!! $answer['answer'] !!}
                    </div>
                </div>
            @endif
            @if($question['type']==\App\Types\QuestionType::GROUP)
                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                    问题
                </div>
                <div style="padding:10px 0;">
                    <?php $number =1; ?>
                    @foreach($items as $index=>$item)
                        <div style="border:1px solid #EEE;border-radius:3px;padding:10px 10px;margin:30px 15px;position:relative;">
                            <div style="line-height:30px;border-radius:15px;min-width:30px;text-align:center;background:#CCC;color:#FFF;position:absolute;left:-15px;top:-15px;">
                                @if($item['question']['type']==\App\Types\QuestionType::FILL && count($item['answers'])>1)
                                    {{$number}}-{{$number+count($item['answers'])-1}}
                                    <?php $number+=count($item['answers']); ?>
                                @else
                                    {{$number}}
                                    <?php $number++; ?>
                                @endif
                            </div>
                            <div>
                                {!! $item['question']['question'] !!}
                            </div>

                            @if($item['question']['type']==\App\Types\QuestionType::SINGLE_CHOICE || $item['question']['type']==\App\Types\QuestionType::MULTI_CHOICES)
                                <div>
                                    <?php $answers = []; ?>
                                    @foreach($item['options'] as $index=>$option)
                                        <div style="padding:0 0 0 20px;">
                                            <div style="float:left;margin:0 0 0 -20px;">{{chr(ord('A')+$index)}}.</div>
                                            {!! $option['option'] !!}
                                        </div>
                                        <?php
                                        if($option['isAnswer']){
                                            $answers[]=chr(ord('A')+$index);
                                        }
                                        ?>
                                    @endforeach
                                </div>
                                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                    答案:
                                    {{join('，',$answers)}}
                                </div>
                            @endif
                            @if($item['question']['type']==\App\Types\QuestionType::TRUE_FALSE)
                                <div>
                                    <?php $answer = null; ?>
                                    @foreach($item['options'] as $index=>$option)
                                        <div style="padding:0 0 0 20px;">
                                            {!! $option['option'] !!}
                                        </div>
                                        <?php
                                        if($option['isAnswer']){
                                            $answer = $option['option'];
                                        }
                                        ?>
                                    @endforeach
                                </div>
                                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                    答案:
                                    {{$answer}}
                                </div>
                            @endif
                            @if($item['question']['type']==\App\Types\QuestionType::FILL)
                                <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;margin:10px 0 0 0;">
                                    答案
                                </div>
                                <div style="padding:10px 0;">
                                    @foreach($item['answers'] as $index=>$answer)
                                        <div style="padding:0 0 0 20px;">
                                            <div style="float:left;margin:0 0 0 -20px;">{{$index+1}}.</div>
                                            {!! $answer['answer'] !!}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($item['question']['type']==\App\Types\QuestionType::TEXT)
                                <div style="padding:10px 0;">
                                    <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                                        答案
                                    </div>
                                    <div style="padding:10px 0;">
                                        {!! $item['answer']['answer'] !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @if($analysis['analysis'])
        <div style="padding:10px 0;">
            <div style="border-left:5px solid #CCC;padding:0 0 0 5px;background:#EEE;">
                解析
            </div>
            <div style="padding:10px 0;">
                {!! $analysis['analysis'] !!}
            </div>
        </div>
    @endif
</div>