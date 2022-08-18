<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use think\facade\Config;
use app\common\cache\setting\ApiCache;
use app\common\model\setting\ApiModel;

/**
 * 接口管理
 */
class ApiService
{
    /**
     * 接口列表
     *
     * @param string $type  list列表，tree树形
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($type = 'list', $where = [], $order = [], $field = '')
    {
        $where[] = ['is_delete', '=', 0];
        if ($type == 'list') {
            $model = new ApiModel();
            $pk = $model->getPk();

            if (empty($field)) {
                $field = $pk . ',api_pid,api_name,api_url,api_sort,is_unlogin,is_unrate,is_disable';
            }
            if (empty($order)) {
                $order = ['api_sort' => 'desc', $pk => 'asc'];
            }

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
            foreach ($data as $k => $v) {
                $data[$k]['children']    = [];
                $data[$k]['hasChildren'] = true;
            }
        } else {
            if (empty($field)) {
                $field = 'api_id,api_pid,api_name,api_url,api_sort,is_unrate,is_unlogin,is_disable';
            }

            $key = $type . md5(serialize($where) . $field);
            $data = ApiCache::get($key);
            if (empty($data)) {
                $model = new ApiModel();
                $pk = $model->getPk();

                if (empty($order)) {
                    $order = ['api_sort' => 'desc', $pk => 'asc'];
                }

                $data = $model->field($field)->where($where)->order($order)->select()->toArray();
                $data = list_to_tree($data, $pk, 'api_pid');

                ApiCache::set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 接口信息
     *
     * @param int  $id   接口id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id = '', $exce = true)
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
                if ($exce) {
                    exception('接口不存在：' . $id);
                }
                return [];
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

        $param[$pk] = $id;

        ApiCache::clear();

        return $param;
    }

    /**
     * 接口修改
     *
     * @param array $ids    接口id
     * @param array $update 接口信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        ApiCache::clear();

        return $update;
    }

    /**
     * 接口删除
     *
     * @param array $ids  接口id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new ApiModel();
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

        ApiCache::clear();

        return $update;
    }

    /**
     * 接口url列表
     *
     * @return array 
     */
    public static function urlList()
    {
        $key = 'url';
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $list = $model->where('is_delete', 0)->column('api_url');
            $list = array_filter($list);

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 接口免登url列表
     *
     * @return array
     */
    public static function unloginUrl()
    {
        $key = 'unlogin';
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $list = $model->where('is_unlogin', 1)->where('is_delete', 0)->column('api_url');
            $list = array_merge($list, Config::get('api.api_is_unlogin', []));
            $list = array_unique(array_filter($list));

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 接口免限url列表
     *
     * @return array
     */
    public static function unrateUrl()
    {
        $key = 'unrate';
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $list = $model->where('is_unrate', 1)->where('is_delete', 0)->column('api_url');
            $list = array_merge($list, Config::get('api.api_is_unrate', []));
            $list = array_unique(array_filter($list));

            ApiCache::set($key, $list);
        }

        return $list;
    }
}
