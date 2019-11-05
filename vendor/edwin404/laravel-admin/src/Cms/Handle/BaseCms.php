<?php

namespace Edwin404\Admin\Cms\Handle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class BaseCms
{
    const TYPE_NONE = 0;
    const TYPE_LIST = 1;
    const TYPE_ADD = 2;
    const TYPE_EDIT = 3;
    const TYPE_VIEW = 4;
    const TYPE_EXPORT = 5;
    const TYPE_DELETE = 6;
    const TYPE_IMPORT = 7;

    protected $defaultConfig = [

        'group' => 'cms',

        // Model或数据表名
        'model_connection' => null,
        'model' => Model::class,

        'joins' => [],


        'primaryKey' => 'id',
        'primaryKeyShow' => true,

        'canView' => false,
        'canAdd' => false,
        'canDelete' => false,
        'canEdit' => false,
        'canExport' => false,
        'canImport' => false,

        'batchOperate' => false,
        'batchDelete' => false,
        'addInNewWindow' => false,
        'editInNewWindow' => false,
        'viewInNewWindow' => false,

        'viewAdd' => 'admin::cms.base.add',
        'viewEdit' => 'admin::cms.base.edit',
        'viewList' => 'admin::cms.base.list',
        'viewView' => 'admin::cms.base.view',
        'viewConfigBase' => 'vendor-config.admin.cms.base',

        'actionList' => '{controller}@{group}List',
        'actionAdd' => '{controller}@{group}Add',
        'actionEdit' => '{controller}@{group}Edit',
        'actionDelete' => '{controller}@{group}Delete',
        'actionView' => '{controller}@{group}View',
        'actionExport' => '{controller}@{group}Export',
        'actionImport' => '{controller}@{group}Import',

        // 用于检查权限的函数 permit_func('\App\DemoController@demo');
        'permitCheck' => null,

        'pageTitle' => 'CMS',

        'pageTitleList' => '{pageTitle}查看',
        'pageTitleAdd' => '{pageTitle}添加',
        'pageTitleEdit' => '{pageTitle}编辑',
        'pageTitleView' => '{pageTitle}查看',
        'pageTitleImport' => '{pageTitle}导入',

        // 显示 前置处理,该hook在一行的所有字段处理结束之后调用,不需要返回值 ProcessView(&$item,&$record)
        'hookProcessView' => '{group}ProcessView',
        // 显示 前置处理,用于处理不存在于Model中的字段,通常情况为组合字段,需要返回当前字段的展示HTML ProcessViewField($key, &$record)
        'hookProcessViewField' => '{group}ProcessViewField',

        // 导出 前置处理,该hook在一行的所有字段处理结束之后调用,不需要返回值 ProcessExport(&$item,&$record)
        'hookProcessExport' => '{group}ProcessExport',
        // 导出 前置处理,用于处理不存在于Model中的字段,通常情况为组合字段,需要返回当前字段的展示HTML ProcessExportField($key, &$record)
        'hookProcessExportField' => '{group}ProcessExportField',

        // 插入调用顺序
        // BaseField[inputGet] ->
        // BaseField[inputProcess] ->
        // BaseField[valueSerialize] ->
        // hookPreAddCheck ->
        // hookPreAdd ->
        // add ->
        // hookPostAdd

        // 更新调用顺序
        // BaseField[inputGet] ->
        // BaseField[inputProcess] ->
        // BaseField[valueSerialize] ->
        // hookPreEditCheck ->
        // hookPreEdit ->
        // add ->
        // hookPostEdit

        // 显示数据顺序
        // hookPostRead -> 通常用于密码隐藏,掩码显示等
        // BaseField[valueUnserialize] ->

        // 字段处理之前 PreInputProcess(&$data)
        'hookPreInputProcess' => '{group}PreInputProcess',

        // 读取 后置处理 PostRead(&$data)
        'hookPostRead' => '{group}PostRead',

        // 插入 前置处理 BeforeAddResolve(&$data)
        'hookBeforeAddResolve' => '{group}BeforeAddResolve',
        // 插入 前置检查 BeforeAddCheck(&$data) ,如果出错直接返回标准错误,正确返回标准正确信息
        'hookBeforeAddCheck' => '{group}BeforeAddCheck',
        // 插入 前置处理,不行需要返回当前字段的展示值 PreAdd(&$data)
        'hookPreAdd' => '{group}PreAdd',
        // 插入 后置处理,不行需要返回当前字段的展示值 PostAdd(&$data), $data已经中包含了 primaryKey
        'hookPostAdd' => '{group}PostAdd',

        // 更新 前置处理 BeforeAddResolve(&$data)
        'hookBeforeEditResolve' => '{group}BeforeEditResolve',
        // 更新 前置检查 BeforeEditCheck(&$data) ,如果出错直接返回标准错误,正确返回标准正确信息
        'hookBeforeEditCheck' => '{group}BeforeEditCheck',
        // 更新 前置处理,不行需要返回当前字段的展示值 PreEdit(&$data)
        'hookPreEdit' => '{group}PreEdit',
        // 更新 后置处理,不行需要返回当前字段的展示值 PostEdit(&$data)
        'hookPostEdit' => '{group}PostEdit',

        // 删除 前置检查 BeforeDeleteCheck(&$data),如果出错直接返回标准错误,正确返回标准正确信息
        'hookBeforeDeleteCheck' => '{group}BeforeDeleteCheck',
        // 删除 前置处理,不行需要返回当前字段的展示值 PreDelete(&$data)
        'hookPreDelete' => '{group}PreDelete',
        // 删除 后置处理,不行需要返回当前字段的展示值 PostDelete(&$data)
        'hookPostDelete' => '{group}PostDelete',

        // 导入 导入文件模板下载,直接返回Excel数据 [] TemplateDataImport()
        'hookTemplateDataImport' => '{group}TemplateDataImport',
        // 导入 导入数据处理 ProcessDataImport(&$data)
        'hookProcessDataImport' => '{group}ProcessDataImport',

        'fields' => []
    ];

    protected $runtimeData = [
        // 当前处理的Controller
        'controller' => '',
        // 当前处理的Action
        'action' => '',
        // 当前操作可操作的字段
        'fields' => [],
        // 可搜索的字段
        'fieldsSearch' => [],
        // 记录在list增加的html
        'listAppend' => '',
        // 在列表菜单栏增加的html
        'listMenuAppend' => '',
        // 记录在list增加的html
        'addAppend' => '',
        // 记录在list增加的html
        'editAppend' => '',
        // 在列表页批量操作栏的内容
        'batchOperation' => '',
    ];

    function __construct()
    {
        $this->_init();
    }


    protected function fetchFields($type, &$config)
    {
        $map = [
            self::TYPE_LIST => 'list',
            self::TYPE_ADD => 'add',
            self::TYPE_EDIT => 'edit',
            self::TYPE_VIEW => 'view',
            self::TYPE_EXPORT => 'export',
        ];

        if (!isset($map[$type])) {
            return [];
        }
        $fields = [];
        foreach ($config['fields'] as $fieldName => $fieldInfo) {
            if (array_key_exists($map[$type], $fieldInfo) && $fieldInfo[$map[$type]]) {
                $fields[] = $fieldName;
            }
        }
        return $fields;
    }

    protected function processConfig($config, $type)
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
            $mergedConfig[$k] = str_replace([
                '{controller}', '{group}', '{pageTitle}'
            ], [
                $controller, $mergedConfig['group'], $mergedConfig['pageTitle']
            ], $v);
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

        // 按需加载
        switch ($type) {
            case self::TYPE_LIST:
                if (view()->exists($mergedConfig['viewConfigBase'] . '.listAppend')) {
                    $this->runtimeData['listAppend'] = View::make($mergedConfig['viewConfigBase'] . '.listAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                if (view()->exists($mergedConfig['viewConfigBase'] . '.batchOperation')) {
                    $this->runtimeData['batchOperation'] = View::make($mergedConfig['viewConfigBase'] . '.batchOperation', [
                        'config' => $mergedConfig
                    ])->render();
                }
                if (view()->exists($mergedConfig['viewConfigBase'] . '.listMenuAppend')) {
                    $this->runtimeData['listMenuAppend'] = View::make($mergedConfig['viewConfigBase'] . '.listMenuAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                break;
            case self::TYPE_ADD:
                if (view()->exists($mergedConfig['viewConfigBase'] . '.addAppend')) {
                    $this->runtimeData['addAppend'] = View::make($mergedConfig['viewConfigBase'] . '.addAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                break;
            case self::TYPE_EDIT:
                if (view()->exists($mergedConfig['viewConfigBase'] . '.editAppend')) {
                    $this->runtimeData['editAppend'] = View::make($mergedConfig['viewConfigBase'] . '.editAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                break;
        }

        $this->runtimeData['fields'] = $this->fetchFields($type, $mergedConfig);
        $this->runtimeData['fieldsSearch'] = [];
        switch ($type) {
            case self::TYPE_LIST:
                foreach ($config['fields'] as $fieldName => $field) {
                    if (array_key_exists('search', $field) && $field['search']) {
                        $this->runtimeData['fieldsSearch'][] = $fieldName;
                    }
                }
                break;
        }

        foreach (array_merge($this->runtimeData['fields'], $this->runtimeData['fieldsSearch']) as $key) {
            $field = &$mergedConfig['fields'][$key];
            $field['_instance'] = new $field['type']($this);
            $field['_instance']->key = $key;
            $field['_instance']->field = &$field;
            foreach ($field as $k => &$v) {
                if (!in_array($k, ['type', '_instance'])) {
                    $field['_instance']->$k = $v;
                }
            }
        }

        // 权限检查
        if ($mergedConfig['permitCheck']) {
            if ($mergedConfig['canView']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionView'])) {
                    $mergedConfig['canView'] = false;
                }
            }
            if ($mergedConfig['canAdd']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionAdd'])) {
                    $mergedConfig['canAdd'] = false;
                }
            }
            if ($mergedConfig['canEdit']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionEdit'])) {
                    $mergedConfig['canEdit'] = false;
                }
            }
            if ($mergedConfig['canDelete']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionDelete'])) {
                    $mergedConfig['canDelete'] = false;
                }
            }
            if ($mergedConfig['canExport']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionExport'])) {
                    $mergedConfig['canExport'] = false;
                }
            }
            if ($mergedConfig['canImport']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionImport'])) {
                    $mergedConfig['canImport'] = false;
                }
            }
        }

        return $mergedConfig;
    }

    public function executeList(&$controllerContext, $config)
    {
        return '[You should override the executeList method in implement class]';
    }

    public function executeAdd(&$controllerContext, $config)
    {
        return '[You should override the executeAdd method in implement class]';
    }

    public function executeEdit(&$controllerContext, $config)
    {
        return '[You should override the executeEdit method in implement class]';
    }

    public function executeDelete(&$controllerContext, $config)
    {
        return '[You should override the executeDelete method in implement class]';
    }

    public function executeView(&$controllerContext, $config)
    {
        return '[You should override the executeView method in implement class]';
    }

    public function executeExport(&$controllerContext, $config)
    {
        return '[You should override the executeExport method in implement class]';
    }

    public function executeImport(&$controllerContext, $config)
    {
        return '[You should override the executeImport method in implement class]';
    }


    protected function _init()
    {
    }
}