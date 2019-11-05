<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=0.5, maximum-scale=5, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <title>
        @if($hasAnswer)
            {{$paper['title']}}-附答案
        @else
            {{$paper['title']}}
        @endif
    </title>
    <link rel="stylesheet" href="@assets('assets/uikit/css/ui.css')">
    <link rel="stylesheet" href="@assets('theme/default/pc/css/question.css')">
    <style type="text/css">
        body {margin:0;padding:0;font-family: 'Helvetica Neue',Arial,'Hiragino Sans GB',STHeiti,'Microsoft YaHei','WenQuanYi Micro Hei',SimSun,Song,sans-serif;background: #F6F6F6;-webkit-print-color-adjust: exact;}
        body .body-container{max-width:800px;padding:2em;margin:0 auto;background:#FFF;}
        body .print-page-breaker{page-break-after:always;}
        #print_box{text-align:center;padding:20px 0;}
        /*#print_tip{text-align:center;color:#999;padding:20px 0;}*/
        /*#markdown_title{padding:150px 10px 10px 10px;font-size:30px;line-height:80px;width:auto;text-align:center;color:#2c3f51;font-weight:bold;max-width:900px;}*/
        /*#markdown_info{text-align:center;padding:50px 0 0 0;}*/
        /*#markdown_info .line{font-weight:normal;}*/
        /*#markdown_toc h2{text-align:center;border-bottom:1px solid #EEE;font-size:20px;line-height:80px;}*/
        /*#markdown_toc .markdown-body{box-sizing:border-box;}*/
        /*#header{text-align:center;}*/
        /*#header img{width:100%;}*/
        /*#markdown_view {box-sizing:border-box;}*/
        @media print {
            /*.tecmz-service-box{display:none !important;}*/
            #print_box{display:none;}
            /*#print_tip{display:none;}*/
            /*table{page-break-inside: avoid;}*/
        }
        @page{
            size:A4;
            margin:15mm;
        }
    </style>
</head>
<body>
    <div id="print_box">
        <button class="uk-button uk-button-large uk-button-primary" onclick="window.print();">点击打印 <i class="uk-icon-print"></i> 或 另存为PDF <i class="uk-icon-file"></i></button>
    </div>
    <div class="body-container">
        <div class="title">
            <h1 style="padding:20px 0;text-align:center;font-size:20px;">{{$paper['title']}}</h1>
        </div>
        <div class="attr">
            <div style="text-align:center;">
                <i class="uk-icon-certificate"></i> 题目总数：{{$paper['questionCount']}}
                &nbsp;&nbsp;
                <i class="uk-icon-check-circle-o"></i> 总分数：{{$paper['totalScore']}}
                &nbsp;&nbsp;
                @if($paper['timeLimitEnable'])
                    <i class="uk-icon-clock-o"></i> 时间：{{$paper['timeLimitValue']}}分钟
                @else
                    <i class="uk-icon-clock-o"></i> 时间：不限时
                @endif
            </div>
        </div>
        <div style="border-top:2px solid #CCC;margin:1em 0;"></div>
        @if(!empty($paperQuestions))
            <?php $paperQuestionNumber = 1; ?>
            @foreach($paperQuestions as $paperQuestion)
                {!! \App\Helpers\QuestionRenderHelper::render($paperQuestion['_questionData'],$paperQuestionNumber,['hasAnswer'=>$hasAnswer]) !!}
                <?php $paperQuestionNumber += $paperQuestion['_questionCount']; ?>
            @endforeach
        @endif
    </div>
</body>
</html>
