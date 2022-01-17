<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件分组
namespace app\common\service\file;

use app\common\cache\file\GroupCache;
use app\common\model\file\GroupModel;

class GroupService
{
    /**
     * 文件分组列表
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
        $model = new GroupModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',group_name,group_desc,group_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['group_sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 文件分组信息
     *
     * @param int $id 文件分组id
     * 
     * @return array
     */
    public static function info($id)
    {
        $info = GroupCache::get($id);
        if (empty($info)) {
            $model = new GroupModel();
            $info = $model->find($id);
            if (empty($info)) {
                exception('文件分组不存在：' . $id);
            }
            $info = $info->toArray();

            GroupCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 文件分组添加
     *
     * @param array $param 文件分组信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new GroupModel();
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
     * 文件分组修改
     *
     * @param array $param 文件分组信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        GroupCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 文件分组删除
     *
     * @param array $ids       文件分组id
     * @param int   $is_delete 是否删除
     * 
     * @return array
     */
    public static function dele($ids, $is_delete = 1)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $update['is_delete']   = $is_delete;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            GroupCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件分组禁用
     *
     * @param array $ids        文件分组id
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable = 0)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            GroupCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}
