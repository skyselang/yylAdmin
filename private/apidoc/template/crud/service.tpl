<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$service.namespace};

use {$tables[0].namespace}\{$tables[0].model_name};
use {$cache.namespace}\{$cache.class_name};

/**
 * {$form.controller_title}
 */
class {$service.class_name}
{
    /**
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
    {foreach $custom.field_add_edit as $k=>$item}
        '{$item}' => '',
    {/foreach}
    ];

    /**
     * {$form.controller_title}列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $model = new {$tables[0].model_name}();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = '{$custom.field_list}';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        if ($page == 0 || $limit == 0) {
            return $model->field($field)->where($where)->order($order)->select()->toArray();
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * {$form.controller_title}信息
     *
     * @param int  $id   {$form.controller_title}id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = {$cache.class_name}::get($id);
        if (empty($info)) {
            $model = new {$tables[0].model_name}();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('{$form.controller_title}不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            {$cache.class_name}::set($id, $info);
        }

        return $info;
    }

    /**
     * {$form.controller_title}添加
     *
     * @param array $param {$form.controller_title}信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new {$tables[0].model_name}();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

     /**
     * {$form.controller_title}修改
     *
     * @param int|array $ids   {$form.controller_title}id
     * @param array     $param {$form.controller_title}信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new {$tables[0].model_name}();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        {$cache.class_name}::del($ids);

        return $param;
    }

    /**
     * {$form.controller_title}删除
     *
     * @param array $ids  {$form.controller_title}id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new {$tables[0].model_name}();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = delete_update();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        {$cache.class_name}::del($ids);

        return $update;
    }
}
