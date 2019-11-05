@extends($_frameLayoutView)

@section('pageTitleMain','绑定用户名')
@section('footer')@endsection

@section('bodyContent')

    <form action="?" method="post" data-ajax-form onsubmit="return false;">

        <div class="pb-form">
            <div class="mui-input-group">
                @if(!empty($_memberUser['phone']))
                    <div class="mui-input-row">
                        <label>账号手机</label>
                        <input type="text" value="{{$_memberUser['phone'] or ''}}" class="mui-input-clear mui-input" readonly />
                    </div>
                @endif
                @if(!empty($_memberUser['email']))
                    <div class="mui-input-row">
                        <label>账号邮箱</label>
                        <input type="text" value="{{$_memberUser['email'] or ''}}" class="mui-input-clear mui-input" readonly />
                    </div>
                @endif
            </div>
            <div class="mui-input-group">
                <div class="mui-input-row">
                    <label>用户名</label>
                    <input name="username" type="text" value="" class="mui-input-clear mui-input" placeholder="请输入用户名">
                </div>
            </div>
            <div class="submit">
                <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">确定绑定</button>
            </div>
        </div>

        <input type="hidden" name="redirect" value="{{$redirect or ''}}" />

    </form>

@endsection