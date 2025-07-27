<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace app\common\controller;

use think\App;
use app\common\utils\ReturnCodeUtils;

/**
 * 基础控制器
 */
abstract class BaseController
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * call方法
     * @param string $name
     * @param array  $args
     */
    public function __call($name, $args)
    {
        exception(lang('方法不存在：') . $name, ReturnCodeUtils::API_URL_ERROR);
    }

    // 初始化
    protected function initialize() {}

    /**
     * 列表分页第几页
     * @param int $default 默认页数
     * @return int
     */
    protected function page($default = 1)
    {
        return $this->request->param('page/d', $default);
    }

    /**
     * 列表分页每页数量
     * @param int $default 默认数量
     * @return int
     */
    protected function limit($default = 10)
    {
        $limit = $this->request->param('limit/d', $default);
        if ($limit >= 500) {
            ini_set('memory_limit', '-1');
        }
        return $limit;
    }

    /**
     * 列表查询条件
     * @param array $other 其它条件，eg：['field', 'exp', 'value'] or [['field', 'exp', 'value']]
     */
    protected function where($other = [])
    {
        $search_mode = $this->request->param('search_mode/s', 'and');
        $search      = $this->request->param('search/a', []);

        if (!in_array($search_mode, ['and', 'or'])) {
            exception(lang('匹配模式错误：') . $search_mode);
        }

        $where_exps = where_exps();
        $exps = array_column($where_exps, 'exp');
        foreach ($search as $key => $val) {
            $index = $key + 1;
            $field = $val['field'] ?? '';
            $exp   = $val['exp'] ?? '';
            $value = $val['value'] ?? '';

            if ($field && $exp && $value !== '') {
                $exp_name = where_exp_name($exp);
                if (!in_array($exp, $exps)) {
                    exception(lang('查询方式错误：') . $index . '：' . $exp_name);
                }

                if (in_array($exp, ['like', 'not like', '=', '<>', '>=', '<', '<=']) && is_array($value)) {
                    exception(lang('查询方式错误：') . $index . '：' . $exp_name . '；' . lang('请选择其它方式'));
                }

                if ($exp === 'like' || $exp === 'not like') {
                    $value = '%' . $value . '%';
                } elseif ($exp === 'between' || $exp === 'not between') {
                    if (!is_array($value)) {
                        $value = str_replace('，', ',', $value);
                        $value = explode(',', $value);
                    }
                    $value = [$value[0] ?? '', $value[count($value) - 1] ?? ''];
                } elseif ($exp === 'in' || $exp === 'not in') {
                    if (!is_array($value)) {
                        $value = str_replace('，', ',', $value);
                    }
                }

                $where[] = [$field, $exp, $value];
            } elseif ($field && $exp) {
                if ($exp === 'null' || $exp === 'not null') {
                    $where[] = [$field, '=', $exp];
                } elseif ($exp === 'empty') {
                    $where[] = [$field, '=', ''];
                } elseif ($exp === 'not empty') {
                    $where[] = [$field, '<>', ''];
                }
            }
        }

        if ($other) {
            foreach ($other as $val) {
                if (is_array($val)) {
                    $where[] = $val;
                } else {
                    $where[] = $other;
                    break;
                }
            }
        }

        return $where ?? [];
    }

    /**
     * 列表排序
     * @param array $default 默认排序
     */
    protected function order($default = [])
    {
        $order = $default;
        $sort_field = $this->request->param('sort_field/s', '');
        $sort_value = $this->request->param('sort_value/s', '');
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        return $order;
    }

    /**
     * 获取当前请求的部分参数
     * @param array        $fields 字段，eg：['field1','field2'=>0,'field3/b'=>false]
     * @param string|array $filter 过滤方法
     */
    protected function params($fields = [], $filter = '')
    {
        $params = [];
        foreach ($fields as $key => $val) {
            $name = '';
            $type = '';
            $default = null;

            if (is_numeric($key)) {
                $name = $val;
            } else {
                $name = $key;
                $default = $val;
            }

            if (strpos($name, '/')) {
                [$name, $type] = explode('/', $name);
            }

            if ($name) {
                $params[$name] = $this->request->param($name . '/' . $type, $default, $filter);
            }
        }

        return $params;
    }

    /**
     * 获取当前请求的参数
     * @access public
     * @param  string|array $name    变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter  过滤方法
     * @return mixed
     */
    public function param($name = '', $default = null, $filter = '')
    {
        return $this->request->param($name, $default, $filter);
    }
}
