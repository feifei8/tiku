@extends('admin::frame')

@section('pageTitle','短信发送')

@section('bodyContent')

    <div class="block admin-form">
        <form action="?" class="uk-form" method="post" data-ajax-form="">
            <div style="font-size:13px;">
                <table class="uk-table uk-table-radius uk-table-striped cms-config-form">
                    <tbody>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">
                                    <b>开启短信发送:</b>
                                </div>
                                <div class="field">
                                    <label>
                                        <input type="radio" name="systemSmsEnable" value="1" @if(\Edwin404\Config\Facades\ConfigFacade::get('systemSmsEnable')) checked @endif> 是
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" name="systemSmsEnable" value="0" @if(!\Edwin404\Config\Facades\ConfigFacade::get('systemSmsEnable')) checked @endif> 否
                                    </label>
                                </div>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="widget-tab widget-tab-secondary">
                                <input type="hidden" data-tab-value name="systemSmsSender" value="{{\Edwin404\Config\Facades\ConfigFacade::get('systemSmsSender')}}" />
                                <div class="head">
                                    <div class="menu">
                                        <a href="javascript:;" data-value="{{\Edwin404\Tecmz\Types\SmsSender::TECMZ}}">{{\Edwin404\Base\Support\TypeHelper::name(\Edwin404\Tecmz\Types\SmsSender::class,\Edwin404\Tecmz\Types\SmsSender::TECMZ)}}</a>
                                    </div>
                                </div>
                                <div class="body">
                                    <div class="item">
                                        <div class="line">
                                            <div class="label">
                                                <b>AppKey：</b>
                                            </div>
                                            <div class="field">
                                                <input type="text" name="systemSmsSenderTecmzAppKey" value="{{\Edwin404\Config\Facades\ConfigFacade::get('systemSmsSenderTecmzAppKey','')}}" />
                                            </div>
                                        </div>
                                        <div class="line">
                                            <div class="label">
                                                <b>模板ID：</b>
                                            </div>
                                            <div class="field">
                                                <input type="text" name="systemSmsSenderTecmzVerifyTemplateId" value="{{\Edwin404\Config\Facades\ConfigFacade::get('systemSmsSenderTecmzVerifyTemplateId','')}}" />
                                            </div>
                                            <div class="desc" style="font-size:12px;">
                                                <div>请在 <a href="http://sms.tecmz.com/" target="_blank">墨子云信</a> 申请短信模板：</div>
                                                <div>
                                                    应用名称：<code>{{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}</code>
                                                </div>
                                                <div>
                                                    短信模板：<code>您的验证码是{verify}。如非本人操作，请忽略本短信</code>
                                                </div>
                                                <div>
                                                    用途：<code>发送验证码</code>
                                                </div>
                                                <div>
                                                    申请成功后填写申请到的 <code>模板ID</code>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="line">
                                            <div class="field">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <button type="submit" class="uk-button uk-button-primary">保存</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

@endsection