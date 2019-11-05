@extends($_frameLayoutView)

@section('pageTitleMain','找回密码')
@section('footer')@endsection

@section('bodyContent')

    <ul class="mui-table-view">
        @if(\Edwin404\Config\Facades\ConfigFacade::get('retrievePhoneEnable',false))
            <li class="mui-table-view-cell">
                <a class="mui-navigate-right" href="/retrieve/phone">
                    <i class="iconfont">&#xe600;</i> 通过手机找回密码
                </a>
            </li>
        @endif
        @if(\Edwin404\Config\Facades\ConfigFacade::get('retrieveEmailEnable',false))
            <li class="mui-table-view-cell">
                <a class="mui-navigate-right" href="/retrieve/email">
                    <i class="iconfont">&#xe604;</i> 通过邮箱找回密码
                </a>
            </li>
        @endif
    </ul>


@endsection