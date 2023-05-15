<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use app\common\cache\system\DeptCache;
use app\common\cache\system\UserCache;
use app\common\model\system\DeptModel;
use app\common\model\system\UserAttributesModel;

/**
 * 部门管理
 */
class DeptService
{
    /**
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
        'dept_id/d'    => 0,
        'dept_pid/d'   => 0,
        'dept_name/s'  => '',
        'dept_abbr/s'  => '',
        'dept_desc/s'  => '',
        'dept_tel/s'   => '',
        'dept_fax/s'   => '',
        'dept_email/s' => '',
        'dept_addr/s'  => '',
        'sort/d'       => 250,
    ];

    /**
     * 部门列表
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
        $model = new DeptModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',dept_pid,dept_abbr,dept_name,dept_desc,sort,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $key = where_cache_key($type, $where, $order, $field);
        $data = DeptCache::get($key);
        if (empty($data)) {
            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
            if ($type == 'tree') {
                $data = array_to_tree($data, $pk, 'dept_pid');
            }
            DeptCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 部门信息
     * 
     * @param int  $id   部门id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = DeptCache::get($id);
        if (empty($info)) {
            $model = new DeptModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('部门不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            DeptCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 部门添加
     *
     * @param array $param 部门信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new DeptModel();
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

        DeptCache::clear();

        return $param;
    }

    /**
     * 部门修改 
     *     
     * @param int|array $ids   部门id
     * @param array     $param 部门信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new DeptModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        DeptCache::clear();

        return $param;
    }

    /**
     * 部门删除
     * 
     * @param array $ids  部门id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new DeptModel();
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

        DeptCache::clear();

        return $update;
    }

    /**
     * 部门用户
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function user($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return UserService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 部门用户解除
     *
     * @param array $dept_id  部门id
     * @param array $user_ids 用户id
     *
     * @return int
     */
    public static function userRemove($dept_id, $user_ids = [])
    {
        $where[] = ['dept_id', 'in', $dept_id];
        if (empty($user_ids)) {
            $user_ids = UserAttributesModel::where($where)->column('user_id');
        }
        $where[] = ['user_id', 'in', $user_ids];

        $res = UserAttributesModel::where($where)->delete();

        UserCache::del($user_ids);

        return $res;
    }
}
