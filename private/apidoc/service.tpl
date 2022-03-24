<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// {$form.controller_title}Service
namespace {$service.namespace};

use {$cache.namespace}\{$cache.class_name} as {$cache.class_name}Cache;
use {$tables[0].model_path}\{$tables[0].model_name} as {$tables[0].model_name}Model;

class {$service.class_name}
{
    /**
     * {$form.controller_title}列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $model = new {$tables[0].model_name}Model();
        $pk = $model->getPk();
        
        if ($field) {
            $field = str_merge($field, $pk.',{$list.field}');
        } else {
            $field = '*';
        }

        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * {$form.controller_title}信息
     * 
     * @param int $id {$form.controller_title}id
     * 
     * @return array|Exception
     */
    public static function info($id)
    {
        $info = {$cache.class_name}Cache::get($id);
        if (empty($info)) {
            $model = new {$tables[0].model_name}Model();
            $info = $model->find($id);
            if (empty($info)) {
                exception('{$form.controller_title}不存在：' . $id);
            }
            $info = $info->toArray();

            {$cache.class_name}Cache::set($id, $info);
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
        $model = new {$tables[0].model_name}Model();
        $pk = $model->getPk();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * {$form.controller_title}修改 
     *     
     * @param array $param {$form.controller_title}信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $model = new {$tables[0].model_name}Model();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        {$cache.class_name}Cache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * {$form.controller_title}删除
     * 
     * @param array $ids {$form.controller_title}id
     * 
     * @return array|Exception
     */
    public static function dele($ids)
    {
        $model = new {$tables[0].model_name}Model();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            {$cache.class_name}Cache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}
