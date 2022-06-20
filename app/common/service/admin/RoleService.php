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
        $where[] = ['is_delete', '=', 0];
        if (empty($order)) {
            $order = ['role_sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 角色信息
     *
     * @param int  $id   角色id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = RoleCache::get($id);
        if (empty($info)) {
            $model = new RoleModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('角色不存在：' . $id);
                }
                return [];
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

            $admin_menu_pids = str_trim($info['admin_menu_pids']);
            if (empty($admin_menu_pids)) {
                $admin_menu_pids = [];
            } else {
                $admin_menu_pids = explode(',', $admin_menu_pids);
                foreach ($admin_menu_pids as $k => $v) {
                    $admin_menu_pids[$k] = (int) $v;
                }
            }
            $info['admin_menu_pids'] = $admin_menu_pids;

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
        sort($param['admin_menu_pids']);

        $param['admin_menu_ids']  = implode(',', $param['admin_menu_ids']);
        $param['admin_menu_ids']  = str_join($param['admin_menu_ids']);
        $param['admin_menu_pids'] = implode(',', $param['admin_menu_pids']);
        $param['admin_menu_pids'] = str_join($param['admin_menu_pids']);
        $param['create_time']     = datetime();

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
     * @param mixed $ids   角色id
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new RoleModel();
        $pk = $model->getPk();
        unset($update[$pk], $update['ids']);

        if (isset($update['admin_menu_ids'])) {
            sort($update['admin_menu_ids']);
            if (count($update['admin_menu_ids']) > 0) {
                if (empty($update['admin_menu_ids'][0])) {
                    unset($update['admin_menu_ids'][0]);
                }
            }
            $update['admin_menu_ids'] = implode(',', $update['admin_menu_ids']);
            $update['admin_menu_ids'] = str_join($update['admin_menu_ids']);
        }
        if (isset($update['admin_menu_pids'])) {
            sort($update['admin_menu_pids']);
            if (count($update['admin_menu_pids']) > 0) {
                if (empty($update['admin_menu_pids'][0])) {
                    unset($update['admin_menu_pids'][0]);
                }
            }
            $update['admin_menu_pids'] = implode(',', $update['admin_menu_pids']);
            $update['admin_menu_pids'] = str_join($update['admin_menu_pids']);
        }
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        RoleCache::del($ids);

        return $update;
    }

    /**
     * 角色删除
     *
     * @param array $ids  角色id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new RoleModel();
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

        RoleCache::del($ids);

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
        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();
        $admin_role_id = $param[$RolePk];

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        $admin_user_id = $param[$UserPk];

        $admin_role_ids = [];
        $user = UserService::info($admin_user_id);
        if ($user) {
            $admin_role_ids = $user['admin_role_ids'];
            foreach ($admin_role_ids as $k => $v) {
                if ($admin_role_id == $v) {
                    unset($admin_role_ids[$k]);
                }
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

        $update[$RolePk] = $admin_role_id;
        $update[$UserPk] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 角色菜单id
     *
     * @param mixed $id    角色id
     * @param array $where 条件
     *
     * @return array 菜单id
     */
    public static function menu_ids($id, $where = [])
    {
        if (empty($id)) {
            return [];
        }

        if (is_numeric($id)) {
            $admin_role_ids = [$id];
        } elseif (is_array($id)) {
            $admin_role_ids = $id;
        } else {
            $admin_role_ids = explode(',', str_trim($id));
        }

        $RoleModel = new RoleModel();
        $role_menu_ids = $RoleModel
            ->field('admin_menu_ids,admin_menu_pids')
            ->where('admin_role_id', 'in', $admin_role_ids)
            ->where($where)
            ->select();
        $admin_menu_ids = [];
        foreach ($role_menu_ids as $v) {
            $menu_ids = explode(',', trim($v['admin_menu_ids'], ','));
            $menu_pids = explode(',', trim($v['admin_menu_pids'], ','));
            $admin_menu_ids = array_merge($admin_menu_ids, $menu_ids,  $menu_pids);
        }
        $admin_menu_ids = array_unique(array_filter($admin_menu_ids));
        sort($admin_menu_ids);

        return $admin_menu_ids;
    }
}
