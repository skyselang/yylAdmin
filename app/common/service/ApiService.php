<?php
/*
 * @Description  : 接口管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-27
 */

namespace app\common\service;

use think\facade\Db;
use app\common\cache\ApiCache;

class ApiService
{
    /**
     * 接口列表
     * 
     * @param string
     *
     * @return array 树形
     */
    public static function list()
    {
        $key = 'list';
        $api = ApiCache::get($key);

        if (empty($api)) {
            $field = 'api_id,api_pid,api_name,api_url,api_sort,is_disable,is_unlogin,create_time,update_time';
        
            $where[] = ['is_delete', '=', 0];

            $order = ['api_sort' => 'desc', 'api_id' => 'asc'];

            $list = Db::name('api')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $api['list'] = self::toTree($list, 0);

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
                $where[] = ['api_id', '=',  $api_id];
            } else {
                $where[] = ['is_delete', '=', 0];
                $where[] = ['api_url', '=',  $api_id];
            }

            $api = Db::name('api')
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

        $api_id = Db::name('api')
            ->insertGetId($param);

        if (empty($api_id)) {
            exception();
        }

        $param['api_id'] = $api_id;

        ApiCache::del();

        return $param;
    }

    /**
     * 接口修改
     *
     * @param array  $param  接口信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $api_id = $param['api_id'];

        unset($param['api_id']);

        $api = self::info($api_id);

        $param['update_time'] = datetime();

        $update = Db::name('api')
            ->where('api_id', '=', $api_id)
            ->update($param);

        if (empty($update)) {
            exception();
        }

        $param['api_id'] = $api_id;

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

        return $param;
    }

    /**
     * 接口删除
     *
     * @param integer $api_id 接口id
     * 
     * @return array
     */
    public static function dele($api_id)
    {
        $api = self::info($api_id);

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('api')
            ->where('api_id', '=', $api_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['api_id'] = $api_id;

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

        return $update;
    }

    /**
     * 接口是否禁用
     *
     * @param array $param 接口信息
     * 
     * @return array
     */
    public static function disable($param)
    {
        $api_id = $param['api_id'];

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = datetime();

        $res = Db::name('api')
            ->where('api_id', $api_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $api = self::info($api_id);

        $update['api_id'] = $api_id;

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

        return $update;
    }

    /**
     * 接口是否无需登录
     *
     * @param array $param 接口信息
     * 
     * @return array
     */
    public static function unlogin($param)
    {
        $api_id = $param['api_id'];

        $update['is_unlogin']  = $param['is_unlogin'];
        $update['update_time'] = datetime();

        $res = Db::name('api')
            ->where('api_id', $api_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $api = self::info($api_id);

        $update['api_id'] = $api_id;

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

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

        foreach ($api as $k => $v) {
            if ($v['api_pid'] == $api_id) {
                $children[] = $v['api_id'];
                $children   = array_merge($children, self::getChildren($api, $v['api_id']));
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

        foreach ($api as $k => $v) {
            if ($v['api_pid'] == $api_pid) {
                $v['children'] = self::toTree($api, $v['api_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 接口模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'api_url|api_name')
    {
        $api = Db::name('api')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $api;
    }

    /**
     * 接口精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function equQuery($keyword, $field = 'api_url|api_name')
    {
        $api = Db::name('api')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $api;
    }

    /**
     * 接口url列表
     *
     * @return array 
     */
    public static function urlList()
    {
        $url_list_key = 'urlList';
        $url_list     = ApiCache::get($url_list_key);
        if (empty($url_list)) {
            $list = Db::name('api')
                ->field('api_url')
                ->where('is_delete', '=', 0)
                ->where('api_url', '<>', '')
                ->order('api_url', 'asc')
                ->select()
                ->toArray();

            $url_list = array_column($list, 'api_url');

            ApiCache::set($url_list_key, $url_list);
        }

        return $url_list;
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
            $list = Db::name('api')
                ->field('api_url')
                ->where('is_delete', '=', 0)
                ->where('is_unlogin', '=', 1)
                ->where('api_url', '<>', '')
                ->order('api_url', 'asc')
                ->select()
                ->toArray();

            $unloginlist = array_column($list, 'api_url');

            ApiCache::set($unloginlist_key, $unloginlist);
        }

        return $unloginlist;
    }
}
