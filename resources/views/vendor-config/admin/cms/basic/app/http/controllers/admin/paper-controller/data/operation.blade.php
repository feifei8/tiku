@if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\App\Http\Controllers\Admin\PaperController@download'))
    <a target="_blank" href="{{action('\App\Http\Controllers\Admin\PaperController@download',['id'=>$record['id'],'hasAnswer'=>1])}}" class="action-btn" data-uk-tooltip title="下载PDF（带答案）"><i class="uk-icon-download"></i></a>
    <a target="_blank" href="{{action('\App\Http\Controllers\Admin\PaperController@download',['id'=>$record['id'],'hasAnswer'=>0])}}" class="action-btn" data-uk-tooltip title="下载PDF（无答案）"><i class="uk-icon-cloud-download"></i></a>
@endif
@if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\App\Http\Controllers\Admin\PaperController@dataView'))
    {actionView}
@endif
@if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\App\Http\Controllers\Admin\PaperController@dataEdit'))
    {actionEdit}
@endif
@if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\App\Http\Controllers\Admin\PaperController@dataDelete'))
    {actionDelete}
@endif