<?php

namespace Edwin404\Admin\Facades;

use Edwin404\Admin\Services\AdminUserService;
use Illuminate\Support\Facades\Facade;

/**
 *
 */
class AdminUserFacade extends Facade
{
    /**
     * @return AdminUserService
     */
    protected static function getFacadeAccessor()
    {
        return 'adminUserService';
    }
}