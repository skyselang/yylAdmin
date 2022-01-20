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
     * 菜单列表
     *
     * @param string $type  list列表，tree树形
     * @param array  $where 搜索条件
     * 
     * @return array 
     */
    public static function list($type = 'list', $where = [])
    {
        if ($where) {
            $model = new ApiModel();
            $pk = $model->getPk();

            $field = $pk . ',api_pid,api_name,api_url,api_sort,is_disable,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['api_sort' => 'desc', $pk => 'asc'];

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
        } else {
            $key = $type;
            $data = ApiCache::get($key);
            if (empty($data)) {
                $model = new ApiModel();
                $pk = $model->getPk();

                $field = $pk . ',api_pid,api_name,api_url,api_sort,is_disable,is_unlogin';

                $where[] = ['is_delete', '=', 0];

                $order = ['api_sort' => 'desc', $pk => 'asc'];

                $data = $model->field($field)->where($where)->order($order)->select()->toArray();

                if ($type == 'tree') {
                    $data = self::toTree($data, 0);
                }

                ApiCache::set($key, $data);
            }
        }

        return $data;
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

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['api_url'];
        }
        ApiCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口修改上级
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

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['api_url'];
        }
        ApiCache::del($ids_arr);

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

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['api_url'];
        }
        ApiCache::del($ids_arr);

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

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['api_url'];
        }
        ApiCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 接口获取所有子级
     *
     * @param array $api    接口列表
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
     * @param int   $api_pid 接口pid
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

        $key = 'urlList';
        $list = ApiCache::get($key);
        if (empty($list)) {
            $list = $model->where('is_delete', 0)->column('api_url');
            $list = array_filter($list);

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 接口无需登录url列表
     *
     * @return array
     */
    public static function unloginUrl()
    {
        $key = 'unloginUrl';
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $list    = $model->where('is_unlogin', 1)->where('is_delete', 0)->column('api_url');
            $unlogin = Config::get('index.api_is_unlogin');
            $list    = array_merge($list, $unlogin);
            $list    = array_unique(array_filter($list));

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 接口无需限率url列表
     *
     * @return array
     */
    public static function unrateUrl()
    {
        $key = 'unrateUrl';
        $list = ApiCache::get($key);
        if (empty($list)) {
            $unrate = Config::get('index.api_is_unrate');
            $list   = array_unique(array_filter($unrate));

            ApiCache::set($key, $list);
        }

        return $list;
    }
}
