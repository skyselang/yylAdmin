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

use think\facade\Db;
use app\common\cache\file\GroupCache;

class GroupService
{
    /**
     * 文件分组列表
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
        if (empty($field)) {
            $field = 'group_id,group_name,group_desc,group_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['group_sort' => 'desc', 'group_id' => 'desc'];
        }

        $count = Db::name('file_group')
            ->where($where)
            ->count('group_id');

        $list = Db::name('file_group')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 文件分组信息
     *
     * @param integer $group_id 文件分组id
     * 
     * @return array
     */
    public static function info($group_id)
    {
        $file_group = GroupCache::get($group_id);
        if (empty($file_group)) {
            $file_group = Db::name('file_group')
                ->where('group_id', $group_id)
                ->find();
            if (empty($file_group)) {
                exception('文件分组不存在：' . $group_id);
            }

            GroupCache::set($group_id, $file_group);
        }

        return $file_group;
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
        $param['create_time'] = datetime();

        $group_id = Db::name('file_group')
            ->insertGetId($param);
        if (empty($group_id)) {
            exception();
        }

        $param['group_id'] = $group_id;

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
        $group_id = $param['group_id'];
        unset($param['group_id']);
        $param['update_time'] = datetime();

        $res = Db::name('file_group')
            ->where('group_id', $group_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param['group_id'] = $group_id;

        GroupCache::del($group_id);

        return $param;
    }

    /**
     * 文件分组删除
     *
     * @param array $group     文件分组列表
     * @param int   $is_delete 是否删除
     * 
     * @return array
     */
    public static function dele($group, $is_delete = 1)
    {
        $group_ids = array_column($group, 'group_id');

        $update['is_delete']   = $is_delete;
        $update['delete_time'] = datetime();

        $res = Db::name('file_group')
            ->where('group_id', 'in', $group_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['group_ids'] = $group_ids;

        foreach ($group_ids as $k => $v) {
            GroupCache::del($v);
        }

        return $update;
    }

    /**
     * 文件分组禁用
     *
     * @param array $group      文件分组列表
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($group, $is_disable = 0)
    {
        $group_ids = array_column($group, 'group_id');

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name('file_group')
            ->where('group_id', 'in', $group_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['group_ids'] = $group_ids;

        foreach ($group_ids as $k => $v) {
            GroupCache::del($v);
        }

        return $update;
    }
}
