<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 角色管理
namespace app\common\service\admin;

use app\common\cache\admin\RoleCache;
use app\common\cache\admin\UserCache;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class RoleService
{
    /**
     * 角色列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',role_name,role_desc,role_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['role_sort' => 'desc', $pk => 'desc'];
        }

        $where[] = ['is_delete', '=', 0];

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 角色信息
     *
     * @param int $id 角色id
     * 
     * @return array
     */
    public static function info($id)
    {
        $info = RoleCache::get($id);
        if (empty($info)) {
            $model = new RoleModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                exception('角色不存在：' . $id);
            }
            $info = $info->toArray();

            $admin_menu_ids = str_trim($info['admin_menu_ids']);
            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            } else {
                $admin_menu_ids = explode(',', $admin_menu_ids);
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
            }

            $info['admin_menu_ids'] = $admin_menu_ids;

            RoleCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 角色添加
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        sort($param['admin_menu_ids']);

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['admin_menu_ids'] = str_join($param['admin_menu_ids']);
        $param['create_time']    = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 角色修改
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        sort($param['admin_menu_ids']);

        if (count($param['admin_menu_ids']) > 0) {
            if (empty($param['admin_menu_ids'][0])) {
                unset($param['admin_menu_ids'][0]);
            }
        }

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['admin_menu_ids'] = str_join($param['admin_menu_ids']);
        $param['update_time']    = datetime();

        $res = $model ->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        RoleCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 角色删除
     *
     * @param array $ids 角色id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            RoleCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 角色禁用
     *
     * @param array $ids        角色id
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            RoleCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 角色用户
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
     * 角色用户解除
     *
     * @param array $param 菜单用户id
     *
     * @return array
     */
    public static function userRemove($param)
    {
        $model = new RoleModel();
        $pk = $model->getPk();
        $admin_role_id = $param[$pk];

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        $admin_user_id = $param[$UserPk];

        $admin_user = UserService::info($admin_user_id);
        $admin_role_ids = $admin_user['admin_role_ids'];
        foreach ($admin_role_ids as $k => $v) {
            if ($admin_role_id == $v) {
                unset($admin_role_ids[$k]);
            }
        }

        if (empty($admin_role_ids)) {
            $admin_role_ids = str_join('');
        } else {
            $admin_role_ids = str_join(implode(',', $admin_role_ids));
        }

        $update['admin_role_ids'] = $admin_role_ids;
        $update['update_time']    = datetime();

        $res = $UserModel->where($UserPk, $admin_user_id)->update($update);
        if (empty($res)) {
            exception();
        }

        UserCache::upd($admin_user_id);

        $update[$pk]     = $admin_role_id;
        $update[$UserPk] = $admin_user_id;

        return $update;
    }

    /**
     * 角色菜单id
     *
     * @param mixed $id 角色id
     *
     * @return array
     */
    public static function getMenuId($id)
    {
        if (empty($id)) {
            return [];
        }

        if (is_numeric($id)) {
            $admin_role_ids[] = $id;
        } elseif (is_array($id)) {
            $admin_role_ids = $id;
        } else {
            $id = str_trim($id);
            $admin_role_ids = explode(',', $id);
        }

        $admin_menu_ids = [];
        foreach ($admin_role_ids as $v) {
            $info = self::info($v);
            $admin_menu_ids = array_merge($admin_menu_ids, $info['admin_menu_ids']);
        }
        $admin_menu_ids = array_unique($admin_menu_ids);

        sort($admin_menu_ids);

        return $admin_menu_ids;
    }
}
