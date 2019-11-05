<?php

namespace Edwin404\Admin\Cms\Handle;

use Edwin404\Admin\Helpers\AdminLogHelper;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ConfigCms
{

    protected $defaultConfig = [

        'group' => 'config',

        'view' => 'admin::cms.config.form',

        'viewConfigBase' => 'vendor-config.admin.cms.config',

        'pageTitle' => 'Config',

        // 配置保存Hook,不需要返回值 ConfigSave($key,$value)
        'hookConfigSave' => '{group}ConfigSave',

        'fields' => []
    ];

    protected $runtimeData = [
        // 当前处理的Controller
        'controller' => '',
        // 当前处理的Action
        'action' => '',
        // 追加HTML
        'listAppend' => '',
    ];

    protected function processConfig($config)
    {
        $routeAction = Route::currentRouteAction();
        if (!Str::startsWith($routeAction, '\\')) {
            $routeAction = '\\' . $routeAction;
        }
        list($controller, $action) = explode('@', $routeAction);

        $this->runtimeData['controller'] = $controller;
        $this->runtimeData['action'] = $action;

        $mergedConfig = $this->defaultConfig;
        foreach ($config as $k => $v) {
            $mergedConfig[$k] = $v;
        }
        foreach ($mergedConfig as $k => $v) {
            if (Str::startsWith($k, 'hook')) {
                $mergedConfig[$k] = str_replace(['{group}'], [$mergedConfig['group']], $v);
            }
        }

        $controller = str_replace(['\\'], ['.'], $this->runtimeData['controller']);
        $controller = preg_replace_callback('/(.)([A-Z])/', function ($matches) {
            $prefix = $matches[1];
            if ($matches[1] != '.') {
                $prefix .= '-';
            }
            return $prefix . strtolower($matches[2]);
        }, $controller);

        $mergedConfig['viewConfigBase'] = $mergedConfig['viewConfigBase'] . $controller . '.' . $mergedConfig['group'];

        if (view()->exists($mergedConfig['viewConfigBase'] . '.append')) {
            $this->runtimeData['listAppend'] = View::make($mergedConfig['viewConfigBase'] . '.append', [
                'config' => $mergedConfig
            ])->render();
        }

        foreach ($mergedConfig['fields'] as $key => &$field) {
            $field['_instance'] = new $field['type']($this);
            $field['_instance']->key = $key;
            $field['_instance']->field = &$field;
            foreach ($field as $k => &$v) {
                if (!in_array($k, ['type', '_instance'])) {
                    $field['_instance']->$k = $v;
                }
            }
        }

        return $mergedConfig;
    }

    public function execute(&$controllerContext, $config)
    {
        $config = $this->processConfig($config);

        if (Request::isMethod('post')) {

            if (Session::get('_adminUserId', null) && env('ADMIN_DEMO_USER_ID', 0) && Session::get('_adminUserId', null) == env('ADMIN_DEMO_USER_ID', 0)) {
                return Response::send(-1, '演示账号禁止该操作');
            }

            $data = [];

            foreach ($config['fields'] as $key => &$field) {
                $data[$key] = $field['_instance']->inputGet(Input::all());
            }

            foreach ($config['fields'] as $key => &$field) {
                $ret = $field['_instance']->inputProcess($data[$key]);
                if ($ret['code']) {
                    return $ret;
                }
                $data[$key] = $ret['data'];
            }

            foreach ($config['fields'] as $key => &$field) {
                $data[$key] = $field['_instance']->valueSerialize($data[$key]);
            }

            $old = [];
            $new = [];
            foreach ($data as $k => $v) {
                $old[$k] = ConfigFacade::get($k);
                $new[$k] = $v;
                ConfigFacade::set($k, $v);
            }
            AdminLogHelper::addInfoLogIfChanged('修改配置', $old, $new);

            $hookConfigSave = method_exists($controllerContext, $config['hookConfigSave']);
            if ($hookConfigSave) {
                foreach ($data as $k => $v) {
                    $func = $config['hookConfigSave'];
                    $controllerContext->$func($k, $v);
                }
            }

            return Response::send(0, '保存成功');
        }

        $data = [];
        foreach ($config['fields'] as $key => &$field) {
            $field = &$config['fields'][$key];
            $data[$key] = $field['_instance']->valueUnserialize(ConfigFacade::get($key, null));
        }

        return view($config['view'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            'data' => $data,
        ]);
    }

}