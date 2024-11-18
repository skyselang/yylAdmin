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
use app\common\service\utils\RetCodeUtils;

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
     *
     * @param  string $name
     * @param  array  $args
     *
     * @return void
     */
    public function __call($name, $args)
    {
        exception('method does not exist：' . $name, RetCodeUtils::API_URL_ERROR);
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 分页第几页
     *
     * @param int $default 默认页数
     *
     * @return int
     */
    protected function page($default = 1)
    {
        return $this->request->param('page/d', $default);
    }

    /**
     * 分页每页数量
     *
     * @param int $default 默认数量
     *
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
     *
     * @param array $other 其它条件，eg：['field', 'exp', 'value'] or [['field', 'exp', 'value']]
     *
     * @return array
     */
    protected function where($other = [])
    {
        $search_field = $this->request->param('search_field/s', '');
        $search_exp   = $this->request->param('search_exp/s', '');
        $search_value = $this->request->param('search_value', '');
        $date_field   = $this->request->param('date_field/s', '');
        $date_value   = $this->request->param('date_value/a', []);

        if (is_array($search_value) && empty($search_value)) {
            $search_value = '';
        }
        if ($search_field && $search_exp && $search_value !== '') {
            $where_exp = where_exps();
            $where_exp = array_column($where_exp, 'exp');
            if (!in_array($search_exp, $where_exp)) {
                exception('查询方式错误：' . $search_exp);
            }

            if (in_array($search_exp, ['like', 'not like', '=', '<>', '>=', '<', '<=']) && is_array($search_value)) {
                exception('查询方式错误：' . $search_exp . '，请选择其它方式');
            }

            if ($search_exp == 'like' || $search_exp == 'not like') {
                $search_value = '%' . $search_value . '%';
            } elseif ($search_exp == 'between' || $search_exp == 'not between') {
                if (!is_array($search_value)) {
                    $search_value = str_replace('，', ',', $search_value);
                    $search_value = explode(',', $search_value);
                }
                $search_value = [$search_value[0] ?? '', $search_value[count($search_value) - 1] ?? ''];
            } elseif ($search_exp == 'in' || $search_exp == 'not in') {
                if (!is_array($search_value)) {
                    $search_value = str_replace('，', ',', $search_value);
                }
            }

            $where[] = [$search_field, $search_exp, $search_value];
        }

        if ($date_field && $date_value) {
            $start_date = $date_value[0] ?? '';
            $end_date   = $date_value[1] ?? '';
            if ($start_date) {
                if (strlen($start_date) > 10) {
                    $where[] = [$date_field, '>=',  $start_date];
                } else {
                    $where[] = [$date_field, '>=',  $start_date . ' 00:00:00'];
                }
            }
            if ($end_date) {
                if (strlen($end_date) > 10) {
                    $where[] = [$date_field, '<=',  $end_date];
                } else {
                    $where[] = [$date_field, '<=',  $end_date . ' 23:59:59'];
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
     *
     * @param array $default 默认排序
     * 
     * @return array
     */
    protected function order($default = [])
    {
        $order = $default;
        $sort_field = $this->request->param('sort_field/s', '');
        $sort_value = $this->request->param('sort_value/s', '');
        if ($sort_field && $sort_value) {
            // is_disable_name ... => is_disable ...
            if (strpos($sort_field, 'is_') === 0) {
                $length = strlen($sort_field);
                if (substr($sort_field, $length - 5) === '_name') {
                    $sort_field = substr($sort_field, 0, $length - 5);
                }
            }
            $order = [$sort_field => $sort_value];
        }
        return $order;
    }

    /**
     * 获取当前请求的部分参数
     * @param array        $fields 字段，eg：['field1','field2'=>0,'field3/b'=>false]
     * @param string|array $filter 过滤方法
     * @return array
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
