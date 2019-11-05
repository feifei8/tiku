<?php

namespace Edwin404\Admin\Http\Controllers\Support;

use Illuminate\Routing\Controller;

class AdminAwareController extends Controller
{
    use AdminUserTrait;

    public function __construct()
    {
        $this->adminUserSetup();
    }
}