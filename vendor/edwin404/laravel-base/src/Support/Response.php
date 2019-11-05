<?php

namespace Edwin404\Base\Support;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Response
{
    public static function generate($code, $msg, $data = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        if (null === $data) {
            unset($response['data']);
        }
        return $response;
    }

    public static function json($code, $msg, $data = null, $redirect = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'redirect' => $redirect,
        ];
        if (null === $redirect) {
            unset($response['redirect']);
        }
        return \Illuminate\Support\Facades\Response::json($response);
    }

    public static function jsonp($data, $callback = null)
    {
        if (empty($callback)) {
            $callback = Input::get('callback', null);
        }
        if (empty($callback)) {
            return \Illuminate\Support\Facades\Response::json($data);
        }
        return \Illuminate\Support\Facades\Response::jsonp($callback, $data);
    }

    public static function send($code, $msg, $data = null, $redirect = null)
    {

        if (Request::ajax()) {
            return self::json($code, $msg, $data, $redirect);
        } else {
            if (empty($msg) && $redirect) {
                return redirect($redirect);
            }
            $response = [
                'code' => $code,
                'msg' => $msg,
                'redirect' => $redirect,
                'data' => $data
            ];
            if (null === $redirect) {
                unset($response['redirect']);
            }
            return view('base::msg', $response);
        }
    }

    /**
     * @deprecated
     */
    public static function schema()
    {
        static $schema = null;
        if (null === $schema) {
            if (Request::secure()) {
                $schema = 'https';
            } else {
                $schema = 'http';
            }
        }
        return $schema;
    }

    public static function download($filename, $content)
    {
        $response = new \Illuminate\Http\Response($content);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

}