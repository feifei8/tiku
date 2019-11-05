<?php

namespace Edwin404\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $table = 'admin_user';

    public function roles()
    {
        return $this->belongsToMany('Edwin404\Admin\Models\AdminRole', 'admin_user_role', 'userId', 'roleId');
    }
}
