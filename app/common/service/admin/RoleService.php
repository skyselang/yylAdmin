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

use think\facade\Db;
use app\common\cache\admin\RoleCache;
use app\common\cache\admin\UserCache;
use app\common\model\admin\UserModel;

class RoleService
{
    // 表名
    protected static $t_name = 'admin_role';
    // 表主键
    protected static $t_pk = 'admin_role_id';

    /**
     * 角色列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = self::$t_pk . ',role_name,role_desc,role_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['role_sort' => 'desc', self::$t_pk => 'desc'];
        }

        $where[] = ['is_delete', '=', 0];

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $pages = ceil($count / $limit);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 角色信息
     *
     * @param integer $admin_role_id 角色id
     * 
     * @return array
     */
    public static function info($admin_role_id)
    {
        $admin_role = RoleCache::get($admin_role_id);
        if (empty($admin_role)) {
            $admin_role = Db::name(self::$t_name)
                ->where(self::$t_pk, $admin_role_id)
                ->find();
            if (empty($admin_role)) {
                exception('角色不存在：' . $admin_role_id);
            }

            $admin_menu_ids = str_trim($admin_role['admin_menu_ids']);
            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            } else {
                $admin_menu_ids = explode(',', $admin_menu_ids);
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
            }

            $admin_role['admin_menu_ids'] = $admin_menu_ids;

            RoleCache::set($admin_role_id, $admin_role);
        }

        return $admin_role;
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
        sort($param['admin_menu_ids']);

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['admin_menu_ids'] = str_join($param['admin_menu_ids']);
        $param['create_time']    = datetime();

        $admin_role_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($admin_role_id)) {
            exception();
        }

        $param[self::$t_pk] = $admin_role_id;

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
        $admin_role_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        sort($param['admin_menu_ids']);

        if (count($param['admin_menu_ids']) > 0) {
            if (empty($param['admin_menu_ids'][0])) {
                unset($param['admin_menu_ids'][0]);
            }
        }

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['admin_menu_ids'] = str_join($param['admin_menu_ids']);
        $param['update_time']    = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_role_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $admin_role_id;

        RoleCache::del($admin_role_id);

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
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
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
     * @param array   $ids        角色id
     * @param integer $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
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
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
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
        $admin_role_id = $param[self::$t_pk];
        $admin_user_id = $param['admin_user_id'];

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

        $AdminUser = new UserModel();
        $res = $AdminUser
            ->where('admin_user_id', $admin_user_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk]     = $admin_role_id;
        $update['admin_user_id'] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 角色菜单id
     *
     * @param mixed $admin_role_id 角色id
     *
     * @return array
     */
    public static function getMenuId($admin_role_id)
    {
        if (empty($admin_role_id)) {
            return [];
        }

        if (is_numeric($admin_role_id)) {
            $admin_role_ids[] = $admin_role_id;
        } elseif (is_array($admin_role_id)) {
            $admin_role_ids = $admin_role_id;
        } else {
            $admin_role_id  = str_trim($admin_role_id);
            $admin_role_ids = explode(',', $admin_role_id);
        }

        $admin_menu_ids = [];
        foreach ($admin_role_ids as $v) {
            $admin_role     = self::info($v);
            $admin_menu_ids = array_merge($admin_menu_ids, $admin_role['admin_menu_ids']);
        }
        $admin_menu_ids = array_unique($admin_menu_ids);

        sort($admin_menu_ids);

        return $admin_menu_ids;
    }
}
