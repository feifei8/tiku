@if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\App\Http\Controllers\Admin\PaperExamController@dataEdit'))
    <a href="{{action('\App\Http\Controllers\Admin\PaperExamController@dataEdit',['_id'=>$record['id']])}}" class="action-btn" data-uk-tooltip title="阅卷"><i class="uk-icon-gavel"></i></a>
@endif
@if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\App\Http\Controllers\Admin\PaperExamController@dataDelete'))
    {actionDelete}
@endif