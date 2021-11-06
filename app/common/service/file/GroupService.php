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
    // 表名
    protected static $t_name = 'file_group';
    // 表主键
    protected static $t_pk = 'group_id';

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
            $field = self::$t_pk . ',group_name,group_desc,group_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['group_sort' => 'desc', self::$t_pk => 'desc'];
        }

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $pages = ceil($count / $limit);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
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
            $file_group = Db::name(self::$t_name)
                ->where(self::$t_pk, $group_id)
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

        $group_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($group_id)) {
            exception();
        }

        $param[self::$t_pk] = $group_id;

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
        $group_id = $param[self::$t_pk];
        unset($param[self::$t_pk]);
        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $group_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $group_id;

        GroupCache::del($group_id);

        return $param;
    }

    /**
     * 文件分组删除
     *
     * @param array   $group     文件分组列表
     * @param integer $is_delete 是否删除
     * 
     * @return array
     */
    public static function dele($group, $is_delete = 1)
    {
        $group_ids = array_column($group, self::$t_pk);

        $update['is_delete']   = $is_delete;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $group_ids)
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
     * @param array   $group      文件分组列表
     * @param integer $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($group, $is_disable = 0)
    {
        $group_ids = array_column($group, self::$t_pk);

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $group_ids)
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
