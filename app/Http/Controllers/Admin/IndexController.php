<?php
namespace App\Http\Controllers\Admin;


use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;

class IndexController extends AdminCheckController
{
    public function index()
    {
        return view('admin.index');
    }
}