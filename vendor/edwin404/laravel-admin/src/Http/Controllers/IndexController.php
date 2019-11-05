<?php

namespace Edwin404\Admin\Http\Controllers;


use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Illuminate\Support\Facades\Session;

class IndexController extends AdminCheckController
{
    public function index()
    {
        return view('index');
    }
}