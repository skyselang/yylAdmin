<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Config;
use app\common\cache\member\ApiCache;
use app\common\cache\member\GroupCache;
use app\common\model\member\ApiModel;
use app\common\model\member\GroupApisModel;

/**
 * 会员接口
 */
class ApiService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'api_id/d'   => '',
        'api_pid/d'  => 0,
        'api_name/s' => '',
        'api_url/s'  => '',
        'sort/d'     => 250,
    ];

    /**
     * 会员接口列表
     *
     * @param string $type  tree树形，list列表
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($type = 'tree', $where = [], $order = [], $field = '')
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',api_pid,api_name,api_url,sort,is_unlogin,is_unauth,is_unrate,is_disable';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'asc'];
        }

        $key = where_cache_key($type, $where, $order, $field);
        $data = ApiCache::get($key);
        if (empty($data)) {
            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
            if ($type == 'tree') {
                $data = array_to_tree($data, $pk, 'api_pid');
            }
            ApiCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员接口信息
     *
     * @param int|string $id   接口id、url
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
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
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['api_url', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('会员接口不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            ApiCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员接口添加
     *
     * @param array $param 接口信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        ApiCache::clear();

        return $param;
    }

    /**
     * 会员接口修改
     *
     * @param int|array $ids   接口id
     * @param array     $param 接口信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        ApiCache::clear();

        return $param;
    }

    /**
     * 会员接口删除
     *
     * @param array $ids  接口id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new ApiModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = delete_update();
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
     * 会员接口分组列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function group($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return GroupService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 会员接口分组解除
     *
     * @param array $api_id    接口id
     * @param array $group_ids 分组id
     *
     * @return int
     */
    public static function groupRemove($api_id, $group_ids = [])
    {
        $where[] = ['api_id', 'in', $api_id];
        if (empty($group_ids)) {
            $group_ids = GroupApisModel::where($where)->column('group_id');
        }
        $where[] = ['group_id', 'in', $group_ids];

        $res = GroupApisModel::where($where)->delete();

        GroupCache::del($group_ids);

        return $res;
    }

    /**
     * 会员接口列表
     * 
     * @param string $type url接口url，id接口id
     *
     * @return array 
     */
    public static function apiList($type = 'url')
    {
        $key = 'api-' . $type;
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $column = 'api_url';
            if ($type == 'id') {
                $column = $model->getPk();
            }

            $list = $model->where([where_delete()])->column($column);
            $list = array_values(array_filter($list));
            sort($list);

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口免登列表
     * 
     * @param string $type url接口url，id接口id
     *
     * @return array
     */
    public static function unloginList($type = 'url')
    {
        $key = 'unlogin-' . $type;
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $column = 'api_url';
            $api_is_unlogin = Config::get('api.api_is_unlogin', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($api_is_unlogin) {
                    $api_is_unlogin = $model->where('api_url', 'in', $api_is_unlogin)->column($column);
                }
            }

            $list = $model->where(where_delete(['is_unlogin', '=', 1]))->column($column);
            $list = array_merge($list, $api_is_unlogin);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口免权列表
     * 
     * @param string $type url接口url，id接口id
     *
     * @return array
     */
    public static function unauthList($type = 'url')
    {
        $key = 'unauth-' . $type;
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $column = 'api_url';
            $api_is_unauth = Config::get('api.api_is_unauth', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($api_is_unauth) {
                    $api_is_unauth = $model->where('api_url', 'in', $api_is_unauth)->column($column);
                }
            }
            $api_is_unlogin = self::unloginList($type);

            $list = $model->where(where_delete(['is_unauth', '=', 1]))->column($column);
            $list = array_merge($list, $api_is_unlogin, $api_is_unauth);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            ApiCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口免限列表
     * 
     * @param string $type url接口url，id接口id
     *
     * @return array
     */
    public static function unrateList($type = 'url')
    {
        $key = 'unrate-' . $type;
        $list = ApiCache::get($key);
        if (empty($list)) {
            $model = new ApiModel();

            $column = 'api_url';
            $api_is_unrate = Config::get('api.api_is_unrate', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($api_is_unrate) {
                    $api_is_unrate = $model->where('api_url', 'in', $api_is_unrate)->column($column);
                }
            }

            $list = $model->where(where_delete(['is_unrate', '=', 1]))->column($column);
            $list = array_merge($list, $api_is_unrate);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            ApiCache::set($key, $list);
        }

        return $list;
    }
}
