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
            $field = $pk . ',group_name,group_desc,group_sort,is_disable,create_time,update_time,delete_time';
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
     * @param int  $id   文件分组id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = GroupCache::get($id);
        if (empty($info)) {
            $model = new GroupModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('文件分组不存在：' . $id);
                }
                return [];
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
     * @param array $ids    文件分组id
     * @param array $update 文件分组信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        GroupCache::del($ids);

        return $update;
    }

    /**
     * 文件分组删除
     *
     * @param array $ids  文件分组id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new GroupModel();
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

        GroupCache::del($ids);

        return $update;
    }
}
