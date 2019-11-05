@extends($_frameLayoutView)

@section('bodyScript')
    <script src="@assets('assets/m/default/home.js')"></script>
@endsection

@section('pageTitle',htmlspecialchars(\Edwin404\Config\Facades\ConfigFacade::get('siteName')))
@section('pageTitleMain',htmlspecialchars(\Edwin404\Config\Facades\ConfigFacade::get('siteName')))
@section('headerLeft')@endsection

@section('bodyContent')

    <div class="pb-home-search">
        <form action="/search" method="get">
            <div class="mui-input-row mui-search">
                <input type="search" name="keywords" class="mui-input-clear" placeholder="搜索 题目/试卷">
            </div>
        </form>
    </div>

    <div class="pb-home-banner swiper-container">
        <div class="swiper-wrapper">
            @if(empty($banners))
                <a href="javascript:;" class="swiper-slide" style="background-image:url('/placeholder/640x320');"></a>
                <a href="javascript:;" class="swiper-slide" style="background-image:url('/placeholder/640x320');"></a>
                <a href="javascript:;" class="swiper-slide" style="background-image:url('/placeholder/640x320');"></a>
            @else
                @foreach($banners as $banner)
                    <a class="swiper-slide"
                       style="background-image:url({{\Edwin404\SmartAssets\Helper\AssetsHelper::fix($banner['image'])}});"
                       @if($banner['link']) href="{{$banner['link']}}" target="_blank" @else href="javascript:;" @endif></a>
                @endforeach
            @endif
        </div>
        <div class="swiper-pagination"></div>
    </div>

    @foreach($tags as $tagGroup)
        <div class="pb-home-tags">
            <div class="head"><i class="iconfont">&#xe667;</i> {{$tagGroup['groupTitle']}}</div>
            <div class="body">
                <div class="box">
                    @foreach($tagGroup['groupTags'] as $tag)
                        <a href="/question/list/{{$tag['id']}}">{{$tag['title']}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

@endsection