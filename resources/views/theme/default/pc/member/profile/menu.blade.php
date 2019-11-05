<div class="pb member-profile-menu">
    <a href="/member/profile_basic" @if($request_path=='/member/profile_basic') class="cur" @endif><i class="uk-icon-list-alt"></i> 我的资料</a>
    <a href="/member/exam" @if($request_path=='/member/exam') class="cur" @endif><i class="uk-icon-gavel"></i> 我的考试</a>
    <a href="/member/favorite_question" @if($request_path=='/member/favorite_question') class="cur" @endif><i class="uk-icon-heart"></i> 收藏的题目</a>
    <a href="/member/profile_avatar" @if($request_path=='/member/profile_avatar') class="cur" @endif><i class="uk-icon-user"></i> 修改头像</a>
    <a href="/member/profile_password" @if($request_path=='/member/profile_password') class="cur" @endif><i class="uk-icon-edit"></i> 修改密码</a>
    <a href="/member/profile_email" @if($request_path=='/member/profile_email') class="cur" @endif><i class="uk-icon-envelope-o"></i> 修改邮箱</a>
    <a href="/member/profile_phone" @if($request_path=='/member/profile_phone') class="cur" @endif><i class="uk-icon-tablet"></i> 修改手机</a>
</div>

