<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use app\common\cache\system\RoleCache;
use app\common\cache\system\UserCache;
use app\common\model\system\RoleModel;
use app\common\model\system\UserAttributesModel;

/**
 * 角色管理
 */
class RoleService
{
    /**
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
        'role_id/d'   => 0,
        'role_name/s' => '',
        'role_desc/s' => '',
        'sort/d'      => 250,
        'menu_ids/a'  => [],
    ];

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
            $field = 'm.' . $pk . ',role_name,role_desc,sort,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $model = $model->alias('m');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'menu_ids' && is_array($wv[2])) {
                $model = $model->join('system_role_menus rm', 'm.role_id=rm.role_id', 'left')->where('rm.menu_id', 'in', $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        if ($page == 0 || $limit == 0) {
            return $model->field($field)->where($where)->order($order)->select()->toArray();
        }

        $count = $model->where($where)->count();
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
     * @return array|Exception
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
            $info = $info->append(['menu_ids'])->toArray();

            RoleCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 角色添加
     *
     * @param array $param 角色信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加菜单
            if (isset($param['menu_ids'])) {
                $model->menus()->saveAll($param['menu_ids']);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 角色修改
     *
     * @param int|array $ids   角色id
     * @param array     $param 角色信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['menu_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改菜单
                    if (isset($param['menu_ids'])) {
                        $info = $info->append(['menu_ids']);
                        relation_update($info, $info['menu_ids'], $param['menu_ids'], 'menus');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        RoleCache::del($ids);

        return $param;
    }

    /**
     * 角色删除
     *
     * @param array $ids  角色id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除菜单
                    $info->menus()->detach();
                }
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
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
     * @param array $role_id  角色id
     * @param array $user_ids 用户id
     *
     * @return int
     */
    public static function userRemove($role_id, $user_ids = [])
    {
        $where[] = ['role_id', 'in', $role_id];
        if (empty($user_ids)) {
            $user_ids = UserAttributesModel::where($where)->column('user_id');
        }
        $where[] = ['user_id', 'in', $user_ids];

        $res = UserAttributesModel::where($where)->delete();

        UserCache::upd($user_ids);

        return $res;
    }

    /**
     * 角色菜单id
     *
     * @param int|array $role_id 角色id
     * @param array     $where   查询条件
     *
     * @return array 菜单id
     */
    public static function menu_ids($role_id, $where = [])
    {
        if (empty($role_id)) {
            return [];
        }

        if (is_numeric($role_id)) {
            $role_id = [$role_id];
        }

        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();
        $role_list = $RoleModel
            ->with(['menus'])
            ->append(['menu_ids'])
            ->where($RolePk, 'in', $role_id)
            ->where($where)
            ->select()
            ->toArray();

        $role_menu_ids = [];
        foreach ($role_list as $role) {
            $role_menu_ids = array_merge($role_menu_ids, $role['menu_ids']);
        }
        $role_menu_ids = array_unique(array_filter($role_menu_ids));

        return $role_menu_ids;
    }
}
