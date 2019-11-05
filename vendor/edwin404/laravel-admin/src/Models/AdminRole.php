<?php

namespace Edwin404\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $table = 'admin_role';

    public function rules()
    {
        return $this->hasMany('Edwin404\Admin\Models\AdminRoleRule', 'roleId', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('Edwin404\Admin\Models\AdminUser', 'admin_user_role', 'roleId', 'userId');
    }
}
