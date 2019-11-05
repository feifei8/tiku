@extends('theme.default.pc.frame')

@section('pageTitleMain','我的消息')

@section('bodyScript')
    @parent
    <script>
        $(function () {
            $('[data-mark-read]').on('click',function () {
                var ids = [];
                $('[data-id]:checked').each(function (i,o) {
                    ids.push($(o).attr('data-id'));
                });
                if(ids.length==0){
                    window.api.dialog.tipError('请先选择消息');
                    return;
                }
                window.api.dialog.loadingOn();
                $.post('/member/message_mark_read',{ids:ids},function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res,{
                        success:function () {
                            $('[data-id]:checked').each(function (i,o) {
                                $(this).closest('tr').remove();
                            });
                            $('[data-member-unread-message-count]').html($('[data-id]').length);
                            if(!$('[data-id]').length){
                                $('[data-member-unread-message-count]').remove();
                                $('[data-message-empty]').show();
                            }
                        }
                    });
                });
            });
            $('[data-mark-read-all]').on('click',function () {
                window.api.dialog.confirm('确定全部标记为已读?',function () {
                    window.api.dialog.loadingOn();
                    $.post('/member/message_mark_read_all',{},function (res) {
                        window.api.dialog.loadingOff();
                        window.api.base.defaultFormCallback(res,{
                            success:function () {
                                $('[data-id]').each(function (i,o) {
                                    $(this).closest('tr').remove();
                                });
                                $('[data-member-unread-message-count]').remove();
                                $('[data-message-empty]').show();
                            }
                        });
                    });
                });
            });
        });
    </script>
@endsection

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/member/{{$_memberUser['id']}}">我的中心</a></li>
                <li class="uk-active"><span>我的通知</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-6">
                @include('theme.default.pc.member.profile.menu')
            </div>
            <div class="uk-width-5-6">
                <div class="pb ">
                    <div class="member-message-list">
                        <div class="action">
                            <a href="javascript:;" class="uk-button" data-mark-read>标为已读</a>
                            <a href="javascript:;" class="uk-button" data-mark-read-all>全部标为已读</a>
                        </div>
                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th width="10">&nbsp;</th>
                                <th width="10">&nbsp;</th>
                                <th>内容</th>
                                <th width="200">时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr data-message-empty @if(!empty($messages)) style="display:none;" @endif>
                                <td colspan="4">
                                    <div class="empty">
                                        没有任何未读消息~
                                    </div>
                                </td>
                            </tr>
                            @foreach($messages as $message)
                                <tr class="unread">
                                    <td><input type="checkbox" data-id="{{$message['id']}}" /></td>
                                    <td><span class="dot"></span></td>
                                    <td>
                                        <div class="message">
                                            {!! $message['content'] !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="time">{{$message['created_at']}}</div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection