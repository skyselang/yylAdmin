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

use think\facade\Config;
use app\common\cache\ApiCache;
use app\common\model\ApiModel;

class ApiService
{
    /**
     * 接口列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',api_pid,api_name,api_url,api_sort,is_disable,is_unlogin';
        }

        if (empty($order)) {
            $order = ['api_sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

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
        $tree = ApiCache::get($key);
        if (empty($tree)) {
            $model = new ApiModel();
            $pk = $model->getPk();

            $field = $pk . ',api_pid,api_name,api_url,api_sort,is_disable,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['api_sort' => 'desc', $pk => 'asc'];

            $list = $model->field($field)->where($where)->order($order)->select()->toArray();

            $tree = self::toTree($list, 0);

            ApiCache::set($key, $tree);
        }

        return $tree;
    }

    /**
     * 接口信息
     *
     * @param int $id 接口id
     * 
     * @return array
     */
    public static function info($id = '')
    {
        if (empty($id)) {
            $id = api_url();
        }

        $info = ApiCache::get($id);
        if (empty($info)) {
            $model = new ApiModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=',  $id];
            } else {
                $where[] = ['api_url', '=',  $id];
                $where[] = ['is_delete', '=', 0];
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                exception('接口不存在：' . $id);
            }
            $info = $info->toArray();

            ApiCache::set($id, $info);
        }

        return $info;
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
        $model = new ApiModel();
        $pk = $model->getPk();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        ApiCache::del();

        $param[$pk] = $id;

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
        $model = new ApiModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);
        $info = self::info($id);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        ApiCache::del([$id, $info['api_url']]);

        $param[$pk] = $id;

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
        $model = new ApiModel();
        $pk = $model->getPk();

        foreach ($ids as $v) {
            self::info($v);
        }

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            ApiCache::del([$v, $info['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口设置父级
     *
     * @param array $ids     接口id
     * @param int   $api_pid 接口pid
     * 
     * @return array
     */
    public static function pid($ids, $api_pid)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        $update['api_pid']     = $api_pid;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            ApiCache::del([$v, $info['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口是否无需登录
     *
     * @param array $ids        接口id
     * @param int   $is_unlogin 是否无需登录
     * 
     * @return array
     */
    public static function unlogin($ids, $is_unlogin)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        $update['is_unlogin']  = $is_unlogin;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            ApiCache::del([$v, $info['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口是否禁用
     *
     * @param array $ids        接口id
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            ApiCache::del([$v, $info['api_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口获取所有子级
     *
     * @param array $api    所有接口
     * @param int   $api_id 接口id
     * 
     * @return array
     */
    public static function getChildren($api, $api_id)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        $children = [];
        foreach ($api as $v) {
            if ($v['api_pid'] == $api_id) {
                $children[] = $v[$pk];
                $children   = array_merge($children, self::getChildren($api, $v[$pk]));
            }
        }

        return $children;
    }

    /**
     * 接口列表转树形
     *
     * @param array $api     接口列表
     * @param int   $api_pid 接口父级id
     * 
     * @return array
     */
    public static function toTree($api, $api_pid)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        $tree = [];
        foreach ($api as $v) {
            if ($v['api_pid'] == $api_pid) {
                $v['children'] = self::toTree($api, $v[$pk]);
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
        $model = new ApiModel();

        $urllist_key = 'urlList';
        $urllist     = ApiCache::get($urllist_key);
        if (empty($urllist)) {
            $urllist = $model->where('is_delete', 0)->column('api_url');
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
        $model = new ApiModel();

        $unloginlist_key = 'unloginList';
        $unloginlist     = ApiCache::get($unloginlist_key);
        if (empty($unloginlist)) {
            $unloginlist = $model->where('is_unlogin', 1)->where('is_delete', 0)->column('api_url');
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
