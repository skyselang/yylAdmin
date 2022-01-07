<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口管理
namespace app\common\service;

use think\facade\Db;
use think\facade\Config;
use app\common\cache\ApiCache;

class ApiService
{
    // 表名
    protected static $t_name = 'api';
    // 表主键
    protected static $t_pk = 'api_id';

    /**
     * 接口列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        if (empty($field)) {
            $field = self::$t_name . ',api_pid,api_name,api_url,api_sort,is_disable,is_unlogin';
        }

        if (empty($order)) {
            $order = ['api_sort' => 'desc', self::$t_pk => 'desc'];
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
     * 接口树形
     *
     * @return array 树形
     */
    public static function tree()
    {
        $key = 'tree';
        $api = ApiCache::get($key);
        if (empty($api)) {
            $field = 'api_id,api_pid,api_name,api_url,api_sort,is_disable,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['api_sort' => 'desc', self::$t_pk => 'asc'];

            $list = Db::name(self::$t_name)
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $api = self::toTree($list, 0);

            ApiCache::set($key, $api);
        }

        return $api;
    }

    /**
     * 接口信息
     *
     * @param integer $api_id 接口id
     * 
     * @return array
     */
    public static function info($api_id = '')
    {
        if (empty($api_id)) {
            $api_id = api_url();
        }

        $api = ApiCache::get($api_id);
        if (empty($api)) {
            if (is_numeric($api_id)) {
                $where[] = [self::$t_pk, '=',  $api_id];
            } else {
                $where[] = ['api_url', '=',  $api_id];
                $where[] = ['is_delete', '=', 0];
            }

            $api = Db::name(self::$t_name)
                ->where($where)
                ->find();
            if (empty($api)) {
                exception('接口不存在：' . $api_id);
            }

            ApiCache::set($api_id, $api);
        }

        return $api;
    }

    /**
     * 接口添加
     *
     * @param array $param 接口信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $api_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($api_id)) {
            exception();
        }

        ApiCache::del();

        $param[self::$t_pk] = $api_id;

        return $param;
    }

    /**
     * 接口修改
     *
     * @param array $param 接口信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $api_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $api = self::info($api_id);

        $param['update_time'] = datetime();

        $update = Db::name(self::$t_name)
            ->where(self::$t_pk, '=', $api_id)
            ->update($param);
        if (empty($update)) {
            exception();
        }

        ApiCache::del([$api_id, $api['api_url']]);

        $param[self::$t_pk] = $api_id;

        return $param;
    }

    /**
     * 接口删除
     *
     * @param array $ids 接口id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        foreach ($ids as $v) {
            self::info($v);
        }

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $api = self::info($v);
            ApiCache::del([$v, $api['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口设置父级
     *
     * @param array   $ids     接口id
     * @param integer $api_pid 接口父级id
     * 
     * @return array
     */
    public static function pid($ids, $api_pid)
    {
        $update['api_pid']     = $api_pid;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $api = self::info($v);
            ApiCache::del([$v, $api['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口是否禁用
     *
     * @param array   $ids        接口id
     * @param integer $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $api = self::info($v);
            ApiCache::del([$v, $api['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口是否无需登录
     *
     * @param array   $ids        接口id
     * @param integer $is_unlogin 是否无需登录
     * 
     * @return array
     */
    public static function unlogin($ids, $is_unlogin)
    {
        $update['is_unlogin']  = $is_unlogin;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $api = self::info($v);
            ApiCache::del([$v, $api['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口获取所有子级
     *
     * @param array   $api    所有接口
     * @param integer $api_id 接口id
     * 
     * @return array
     */
    public static function getChildren($api, $api_id)
    {
        $children = [];

        foreach ($api as $v) {
            if ($v['api_pid'] == $api_id) {
                $children[] = $v[self::$t_pk];
                $children   = array_merge($children, self::getChildren($api, $v[self::$t_pk]));
            }
        }

        return $children;
    }

    /**
     * 接口列表转树形
     *
     * @param array   $api     接口列表
     * @param integer $api_pid 接口父级id
     * 
     * @return array
     */
    public static function toTree($api, $api_pid)
    {
        $tree = [];

        foreach ($api as $v) {
            if ($v['api_pid'] == $api_pid) {
                $v['children'] = self::toTree($api, $v[self::$t_pk]);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 接口url列表
     *
     * @return array 
     */
    public static function urlList()
    {
        $urllist_key = 'urlList';
        $urllist     = ApiCache::get($urllist_key);
        if (empty($urllist)) {
            $urllist = Db::name(self::$t_name)
                ->field('api_url')
                ->where('is_delete', '=', 0)
                ->column('api_url');

            $urllist = array_filter($urllist);

            ApiCache::set($urllist_key, $urllist);
        }

        return $urllist;
    }

    /**
     * 接口无需登录url列表
     *
     * @return array
     */
    public static function unloginList()
    {
        $unloginlist_key = 'unloginList';
        $unloginlist     = ApiCache::get($unloginlist_key);
        if (empty($unloginlist)) {
            $unloginlist = Db::name(self::$t_name)
                ->field('api_url')
                ->where('is_unlogin', '=', 1)
                ->where('is_delete', '=', 0)
                ->column('api_url');

            $api_unlogin = Config::get('index.api_is_unlogin');
            $unloginlist = array_merge($unloginlist, $api_unlogin);
            $unloginlist = array_unique(array_filter($unloginlist));

            ApiCache::set($unloginlist_key, $unloginlist);
        }

        return $unloginlist;
    }

    /**
     * 接口无需限率url列表
     *
     * @return array
     */
    public static function unrateList()
    {
        $unratelist_key = 'unrateList';
        $unratelist     = ApiCache::get($unratelist_key);
        if (empty($unratelist)) {
            $api_unrate = Config::get('index.api_is_unrate');
            $unratelist = array_unique(array_filter($api_unrate));

            ApiCache::set($unratelist_key, $unratelist);
        }

        return $unratelist;
    }
}
