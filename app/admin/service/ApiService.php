<?php
/*
 * @Description  : 接口管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-11
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\ApiCache;

class ApiService
{
    /**
     * 接口列表
     * 
     * @param string $type list列表，tree树形，url链接
     *
     * @return array 
     */
    public static function list($type = 'tree')
    {
        $api = ApiCache::get();

        if (empty($api)) {
            $field = 'api_id,api_pid,api_name,api_url,api_sort,is_disable,is_unauth,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['api_sort' => 'desc', 'api_id' => 'asc'];

            $list = Db::name('api')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $tree = self::toTree($list, 0);
            $url  = array_filter(array_column($list, 'api_url'));

            $api['tree'] = $tree;
            $api['list'] = $list;
            $api['url']  = $url;

            ApiCache::set('', $api);
        }

        if ($type == 'list') {
            $data['count'] = count($api['list']);
            $data['list']  = $api['list'];
        } elseif ($type == 'url') {
            $data['count'] = count($api['url']);
            $data['list']  = $api['url'];
        } else {
            $data['count'] = count($api['tree']);
            $data['list']  = $api['tree'];
        }

        return $data;
    }

    /**
     * 接口信息
     *
     * @param integer $api_id 接口id
     * 
     * @return array
     */
    public static function info($api_id)
    {
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
        $param['create_time'] = date('Y-m-d H:i:s');

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
        $api    = self::info($api_id);

        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('api')
            ->where('api_id', '=', $api_id)
            ->update($param);

        if (empty($update)) {
            exception();
        }

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
        $update['delete_time'] = date('Y-m-d H:i:s');

        $delete = Db::name('api')
            ->where('api_id', '=', $api_id)
            ->update($update);

        if (empty($delete)) {
            exception();
        }

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

        $update['api_id'] = $api_id;

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
        $api    = self::info($api_id);

        $param['is_disable'] = $param['is_disable'];
        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('api')
            ->where('api_id', $api_id)
            ->update($param);

        if (empty($update)) {
            exception();
        }

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

        return $param;
    }

    /**
     * 接口是否无需权限
     *
     * @param array $param 接口信息
     * 
     * @return array
     */
    public static function unauth($param)
    {
        $api_id = $param['api_id'];
        $api    = self::info($api_id);

        $param['is_unauth']   = $param['is_unauth'];
        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('api')
            ->where('api_id', $api_id)
            ->update($param);

        if (empty($update)) {
            exception();
        }

        ApiCache::del();
        ApiCache::del($api_id);
        ApiCache::del($api['api_url']);

        return $param;
    }

    /**
     * 接口所有子级获取
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
     * 接口树形获取
     *
     * @param array   $api 所有接口
     * @param integer $api_pid   接口父级id
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
        $data = Db::name('api')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 接口精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'api_url|api_name')
    {
        $data = Db::name('api')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $data;
    }
}
