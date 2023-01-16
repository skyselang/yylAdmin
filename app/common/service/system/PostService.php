<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use app\common\cache\system\PostCache;
use app\common\cache\system\UserCache;
use app\common\model\system\PostModel;
use app\common\model\system\UserAttributesModel;

/**
 * 职位管理
 */
class PostService
{
    /**
     * 职位列表
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
        $model = new PostModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',post_pid,post_name,post_abbr,post_desc,sort,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'asc'];
        }

        $key = $type . md5(serialize($where) . serialize($order) . $field);
        $data = PostCache::get($key);
        if (empty($data)) {
            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
            if ($type == 'tree') {
                $data = array_to_tree($data, $pk, 'post_pid');
            }
            PostCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 职位信息
     *
     * @param int  $id   职位id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = PostCache::get($id);
        if (empty($info)) {
            $model = new PostModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('职位不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            PostCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 职位添加
     *
     * @param array $param 职位信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new PostModel();
        $pk = $model->getPk();

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        PostCache::clear();

        return $param;
    }

    /**
     * 职位修改
     *
     * @param int|array $ids   职位id
     * @param array     $param 职位信息
     * 
     * @return array
     */
    public static function edit($ids, $param = [])
    {
        $model = new PostModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);
        
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        PostCache::clear();

        return $param;
    }

    /**
     * 职位删除
     *
     * @param array $ids  职位id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new PostModel();
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

        PostCache::clear();

        return $update;
    }

    /**
     * 职位用户
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
     * 职位用户解除
     *
     * @param array $post_id  职位id
     * @param array $user_ids 用户id
     *
     * @return int
     */
    public static function userRemove($post_id, $user_ids = [])
    {
        $where[] = ['post_id', 'in', $post_id];
        if (empty($user_ids)) {
            $user_ids = UserAttributesModel::where($where)->column('user_id');
        }
        $where[] = ['user_id', 'in', $user_ids];

        $res = UserAttributesModel::where($where)->delete();

        UserCache::upd($user_ids);

        return $res;
    }
}
