@extends('theme.default.pc.frame')

@section('pageTitleMain','专项')

@section('bodyScript')
    <script src="@assets('assets/main/default/tags.js')"></script>
@endsection

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>专项</span></li>
            </ul>
        </div>

        <div class="pb pb-tags">
            <div class="head">
                <h2>选择开始训练</h2>
            </div>
            <div class="body uk-form">
                <div class="uk-grid">
                    @foreach($tags as $tagGroup)
                        <div class="uk-width-1-4">
                            <div class="item">
                                <div class="title">
                                    {{$tagGroup['groupTitle']}}
                                </div>
                                <div class="list">
                                    @foreach($tagGroup['groupTags'] as $tag)
                                        <label>
                                            <input type="checkbox" data-tag="{{$tag['id']}}" >
                                            {{$tag['title']}}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="uk-width-1-1 uk-text-center" style="padding:20px 0;">
                        <a href="javascript:;" id="tagSubmit" class="uk-button uk-button-primary uk-button-large">开始训练</a>
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection