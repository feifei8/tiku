<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <div class="widget-images-uploader" data-upload-server="{{env('ADMIN_PATH')}}system/data/image_select_dialog">
        <div class="list">
            <div class="uploader" style="text-align:center;">
                <i class="uk-icon-plus" style="line-height:50px;color:#999;font-size:20px;"></i>
            </div>
            <div class="cf"></div>
        </div>
        <input data-value type="hidden" name="{{$key}}" value="<?php if(!empty($data)){ echo htmlspecialchars(json_encode($data)); } ?>" />
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif
