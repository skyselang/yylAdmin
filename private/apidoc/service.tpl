<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$service.namespace};

use {$cache.namespace}\{$cache.class_name} as {$cache.class_name}Cache;
use {$tables[0].model_path}\{$tables[0].model_name} as {$tables[0].model_name}Model;

/**
 * {$form.controller_title}Service
 */
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
        
        if (empty($field)) {
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
     * @param int  $id   {$form.controller_title}id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = {$cache.class_name}Cache::get($id);
        if (empty($info)) {
            $model = new {$tables[0].model_name}Model();
            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('{$form.controller_title}不存在：' . $id);
                }
                return [];
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
     * @param mixed $ids    {$form.controller_title}id
     * @param array $update {$form.controller_title}信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $update = [])
    {
        $model = new {$tables[0].model_name}Model();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        {$cache.class_name}Cache::del($ids);

        return $update;
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
        $model = new {$tables[0].model_name}Model();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        {$cache.class_name}Cache::del($ids);

        return $update;
    }
}
