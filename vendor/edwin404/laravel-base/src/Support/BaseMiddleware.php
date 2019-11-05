<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Closure;
use Illuminate\Support\Str;

class BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = Request::path();
        if (empty($path)) {
            $path = '/';
        } else if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        View::share('request_path', $path);
        Session::flash('request_path', $path);

        return $next($request);
    }
}