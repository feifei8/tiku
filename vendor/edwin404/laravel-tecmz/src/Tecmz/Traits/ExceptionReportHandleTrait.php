<?php

namespace Edwin404\Tecmz\Traits;


use Edwin404\Base\Support\CurlHelper;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionReportHandleTrait
{
    private function errorReportCheck($exception)
    {
        try {
            $needReport = true;
            if ($needReport && $exception instanceof NotFoundHttpException) {
                $needReport = false;
            }
            if ($needReport && $exception instanceof MethodNotAllowedHttpException) {
                $needReport = false;
            }
            if ($needReport) {
                $errorReportUrl = env('ERROR_REPORT_URL', null);
                if ($errorReportUrl) {
                    $error = [];
                    $error['url'] = Request::url();
                    $error['file'] = $exception->getFile() . ':' . $exception->getLine();
                    $error['message'] = $exception->getMessage();
                    foreach ($error as &$v) {
                        $v = str_replace(base_path(), '', $v);
                    }
                    @file_get_contents($errorReportUrl . '?data=' . urlencode(json_encode($error)));
                }
            }
        } catch (\Exception $e) {
            // do nothing
        }
    }
}