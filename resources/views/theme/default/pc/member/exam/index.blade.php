@extends('theme.default.pc.frame')

@section('pageTitleMain','我的考试')


@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/member/{{$_memberUser['id']}}">我的中心</a></li>
                <li class="uk-active"><span>我的考试</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-6">
                @include('theme.default.pc.member.profile.menu')
            </div>
            <div class="uk-width-5-6">

                <div class="pb pb-member-exam-list">
                    <div class="head">
                        <h2>我的考试</h2>
                    </div>
                    <div class="body">
                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th>考试</th>
                                <th width="80">状态</th>
                                <th width="80">成绩</th>
                                <th width="160">时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(empty($paperExams))
                                <tr>
                                    <td colspan="3">
                                        <div class="empty">
                                            还没有任何考试~
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach($paperExams as $paperExam)
                                    <tr class="unread">
                                        <td>
                                            <a href="/member/exam/{{$paperExam['id']}}">{{$paperExam['_paper']['title']}}</a>
                                        </td>
                                        <td>
                                            @if($paperExam['status']==\App\Types\PaperExamStatus::DOING)
                                                <div class="uk-text-danger">正在考试</div>
                                            @endif
                                            @if($paperExam['status']==\App\Types\PaperExamStatus::SUBMITTED)
                                                <div class="uk-text-success">考试完成</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($paperExam['isJudge'])
                                                {{$paperExam['score']}}
                                                /
                                                {{$paperExam['_paper']['totalScore']}}
                                            @else
                                                <div class="uk-text-muted">正在阅卷</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="uk-text-muted">{{$paperExam['created_at']}}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="page-container">
                    {!! $pageHtml !!}
                </div>

            </div>
        </div>

    </div>



@endsection