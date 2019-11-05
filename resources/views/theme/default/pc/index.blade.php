@extends('theme.default.pc.frame')

@section('pageTitle',\Edwin404\Config\Facades\ConfigFacade::get('siteName'))

@section('bodyScript')
    <script src="@assets('assets/main/default/home.js')"></script>
@endsection

@section('bodyContent')

    <div class="main-container">

        <div class="uk-grid">
            <div class="uk-width-1-4">
                <div class="pb pb-category">
                    <div class="body">
                        @foreach($tags as $tagGroup)
                            <a class="title" href="javascript:;"><i class="uk-icon-circle-o"></i> {{$tagGroup['groupTitle']}}</a>
                            <div class="title-box">
                                @foreach($tagGroup['groupTags'] as $tag)
                                    <a href="/question/list/{{$tag['id']}}">{{$tag['title']}}</a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="uk-width-3-4">

                <div class="pb">
                    <div class="body">
                        <div class="pb-search-home">
                            <input type="text" placeholder="搜索 题目/试卷" id="keyword" onkeypress="if(event.keyCode==13){window.location.href='/search?keywords='+window.api.util.urlencode($('#keyword').val());}" />
                            <a href="javascript:;" onclick="window.location.href='/search?keywords='+window.api.util.urlencode($('#keyword').val());"><span class="uk-icon-search"></span> 搜索</a>
                        </div>
                    </div>
                </div>

                <div class="pb pb-banner">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            @if(empty($banners))
                                <a href="javascript:;" class="swiper-slide" style="background-image:url('/placeholder/750x300');"></a>
                                <a href="javascript:;" class="swiper-slide" style="background-image:url('/placeholder/750x300');"></a>
                                <a href="javascript:;" class="swiper-slide" style="background-image:url('/placeholder/750x300');"></a>
                            @else
                                @foreach($banners as $banner)
                                    <a class="swiper-slide"
                                       style="background-image:url({{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($banner['image'])}});"
                                       @if($banner['link']) href="{{$banner['link']}}" target="_blank" @else href="javascript:;" @endif></a>
                                @endforeach
                            @endif
                        </div>
                        <div class="swiper-pagination swiper-pagination-white"></div>
                        <div class="swiper-button-next swiper-button-white"></div>
                        <div class="swiper-button-prev swiper-button-white"></div>
                    </div>
                </div>

                <div class="pb pb-question-list">
                    <div class="head">
                        <h2>最新题目</h2>
                    </div>
                    <div class="body">
                        <div class="empty" style="display:none;">
                            暂无记录
                        </div>
                        @foreach($latestQuestions as $question)
                            <div class="item">
                                <div class="title">
                                    <a href="/question/view/{{$question['alias']}}">
                                        [{{\Edwin404\Base\Support\TypeHelper::name(\App\Types\QuestionType::class,$question['type'])}}]
                                        {{\Edwin404\Base\Support\HtmlHelper::text($question['question'],100)}}
                                    </a>
                                </div>
                                <div class="tags">
                                    <div class="right">
                                        @if($question['source'])
                                        <span>来源 {{$question['source']}}</span>
                                        |
                                        @endif
                                        <span>正确率 {{$question['testCount']>0?sprintf('%d%%',$question['passCount']*100/$question['testCount']):'-'}}</span>
                                        |
                                        <span>评论 {{$question['commentCount'] or 0}}</span>
                                        |
                                        <span>点击 {{$question['clickCount']}}</span>
                                    </div>
                                    @foreach($question['tag'] as $tag)
                                        <a href="/question/list/{{$tag['id']}}" target="_blank">{{$tag['title']}}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <a class="more" href="/question/list">
                            查看更多 <i class="uk-icon-angle-double-down"></i>
                        </a>
                    </div>
                </div>

                <div class="pb pb-partner">
                    <div class="head">
                        <h2>合作伙伴</h2>
                    </div>
                    <div class="body">
                        <div class="uk-grid">
                            @foreach($partners as $partner)
                            <div class="uk-width-1-6">
                                <a class="item" href="{{$partner['link']}}" target="_blank" title="{{$partner['title']}}">
                                    <div class="cover">
                                        <img data-src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($partner['logo'])}}">
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>


@endsection