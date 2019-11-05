@extends('admin::frame')

@section('pageTitle','角色')

@section('bodyMenu')
    @if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@userRoleEdit'))
        <a href="#" data-dialog-request="{{action('\Edwin404\Admin\Http\Controllers\SystemController@userRoleEdit')}}" class="btn"><i class="uk-icon-plus"></i> 增加</a>
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