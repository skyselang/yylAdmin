<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace app\common;

use think\App;

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

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 获取当前请求的变量
     *
     * @param string $name    变量名/变量修饰符（s字符串 d整型 b布尔 a数组 f浮点）
     * @param mixed  $default 默认值
     * @param string $filter  过滤方法
     *
     * @return mixed
     */
    protected function param($name = '', $default = null, $filter = '')
    {
        return $this->request->param($name, $default, $filter);
    }

    /**
     * 分页第几页
     *
     * @param int $default 默认值
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
     * @param int $default 默认值
     *
     * @return int
     */
    protected function limit($default = 10)
    {
        return $this->request->param('limit/d', $default);
    }

    /**
     * 排序
     *
     * @param array $default 默认值
     * 
     * @return array
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
     * 条件
     *
     * @param array  $other 其它条件，eg：['field', 'exp', 'value'] or [['field', 'exp', 'value']]
     * @param string $field 表达式in字段，eg：'field1' or 'field1,field2'
     * @param bool   $hide  是否隐藏超管记录 true|false
     *
     * @return array
     */
    protected function where($other = [], $field = '', $hide = false)
    {
        $where = [];
        if ($other) {
            foreach ($other as $value) {
                if (is_array($value)) {
                    $where[] = $value;
                } else {
                    $where[] = $other;
                    break;
                }
            }
        }

        if ($hide) {
            $admin_super_hide_where = admin_super_hide_where();
            if ($admin_super_hide_where) {
                $where[] = $admin_super_hide_where;
            }
        }

        $search_field = $this->request->param('search_field/s', '');
        $search_value = $this->request->param('search_value/s', '');
        $date_field   = $this->request->param('date_field/s', '');
        $date_value   = $this->request->param('date_value/a', []);

        if ($search_field && $search_value !== '') {
            $field_arr = explode(',', $field);
            if (in_array($search_field, $field_arr)) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }

        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        return $where;
    }

    /**
     * 是否返回额外数据
     * 
     * @param int $default 默认值
     * 
     * @return bool|int
     */
    protected function isExtra($default = 0)
    {
        return $this->request->param('is_extra/d', $default);
    }
}
