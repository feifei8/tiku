@extends('admin::frame')

@section('pageTitle','管理员')

@section('bodyMenu')
    @if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@userEdit'))
        <a href="#" data-dialog-request="{{action('\Edwin404\Admin\Http\Controllers\SystemController@userEdit')}}" class="btn"><i class="uk-icon-plus"></i> 增加</a>
    @endif
@endsection

@section('bodyContent')

    <div class="block">

        <div data-admin-lister class="admin-lister-container">

            <div class="lister-table"></div>
            <div class="page-container"></div>

        </div>

    </div>

@endsection