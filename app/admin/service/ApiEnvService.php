<?php
/*
 * @Description  : 接口环境
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-01-14
 * @LastEditTime : 2021-03-20
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\ApiEnvCache;

class ApiEnvService
{
    /**
     * 接口环境列表
     *
     * @param array   $where   条件
     * @param integer $page    页数
     * @param integer $limit   数量
     * @param array   $order   排序
     * @param string  $field   字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'api_env_id,name,host,sort,header,create_time,update_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['sort' => 'desc', 'api_env_id' => 'desc'];
        }

        $count = Db::name('api_env')
            ->where($where)
            ->count('api_env_id');

        $list = Db::name('api_env')
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
     * 接口环境信息
     *
     * @param integer $api_env_id 接口环境id
     * 
     * @return array
     */
    public static function info($api_env_id)
    {
        $api_env = ApiEnvCache::get($api_env_id);

        if (empty($api_env)) {
            $api_env = Db::name('api_env')
                ->where('api_env_id', $api_env_id)
                ->find();

            if (empty($api_env)) {
                exception('接口环境不存在：' . $api_env_id);
            }

            ApiEnvCache::set($api_env_id, $api_env);
        }

        return $api_env;
    }

    /**
     * 接口环境添加
     *
     * @param array $param 接口环境信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $api_env_id = Db::name('api_env')
            ->insertGetId($param);

        if (empty($api_env_id)) {
            exception();
        }

        $param['api_env_id'] = $api_env_id;

        return $param;
    }

    /**
     * 接口环境修改
     *
     * @param array $param 接口环境信息
     * 
     * @return array
     */
    public static function edit($param = [], $method = 'get')
    {
        $api_env_id = $param['api_env_id'];

        if ($method == 'get') {
            $data = self::info($api_env_id);

            return $data;
        } else {
            unset($param['api_env_id']);

            $param['update_time'] = datetime();

            $res = Db::name('api_env')
                ->where('api_env_id', $api_env_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['api_env_id'] = $api_env_id;

            ApiEnvCache::del($api_env_id);

            return $param;
        }
    }

    /**
     * 接口环境删除
     *
     * @param integer $api_env_id 接口环境id
     * 
     * @return array
     */
    public static function dele($api_env_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('api_env')
            ->where('api_env_id', $api_env_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['api_env_id'] = $api_env_id;

        ApiEnvCache::del($api_env_id);

        return $update;
    }
}
