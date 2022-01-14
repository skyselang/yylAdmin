<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 菜单管理
namespace app\common\service\admin;

use think\facade\Config;
use app\common\cache\admin\MenuCache;
use app\common\cache\admin\RoleCache;
use app\common\cache\admin\UserCache;
use app\common\model\admin\MenuModel;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class MenuService
{
    /**
     * 菜单列表
     *
     * @return array 
     */
    public static function list()
    {
        $key  = 'list';
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();
            $pk = $model->getPk();

            $field = $pk . ',menu_pid,menu_name,menu_url,is_unauth,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['menu_sort' => 'desc', $pk => 'asc'];

            $list = $model->field($field)->where($where)->order($order)->select()->toArray();

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单树形
     *
     * @return array
     */
    public static function tree()
    {
        $key  = 'tree';
        $tree = MenuCache::get($key);
        if (empty($tree)) {
            $model = new MenuModel();
            $pk = $model->getPk();

            $field = $pk . ',menu_pid,menu_name,menu_url,menu_sort,is_disable,is_unauth,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['menu_sort' => 'desc', $pk => 'asc'];

            $list = $model->field($field)->where($where)->order($order)->select()->toArray();

            $tree = self::toTree($list, 0);

            MenuCache::set($key, $tree);
        }

        return $tree;
    }

    /**
     * 菜单信息
     *
     * @param int $id 菜单id
     * 
     * @return array
     */
    public static function info($id = '')
    {
        if (empty($id)) {
            $id = menu_url();
        }

        $info = MenuCache::get($id);
        if (empty($info)) {
            $model = new MenuModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=',  $id];
            } else {
                $where[] = ['menu_url', '=',  $id];
                $where[] = ['is_delete', '=', 0];
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                exception('菜单不存在：' . $id);
            }
            $info = $info->toArray();

            MenuCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 菜单添加
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $add = false;
        $add_arr = ['list' => '列表', 'info' => '信息', 'add' => '添加', 'edit' => '修改', 'dele' => '删除'];
        foreach ($add_arr as $k => $v) {
            $add_key = '';
            $add_key = 'add_' . $k;
            if ($param[$add_key]) {
                $add = true;
            }
        }

        $model = new MenuModel();
        $pk = $model->getPk();

        if ($add) {
            if (empty($param['menu_url'])) {
                exception('请输入菜单链接：应用/控制器，不含操作');
            }

            $errmsg = '';
            // 启动事务
            $model->startTrans();
            try {
                $id = $model->strict(false)->insertGetId($param);

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key]) {
                        $menu_url = '';
                        $menu_url = $param['menu_url'] . '/' . $k;
                        $exist_where[] = ['menu_url', '=', $menu_url];
                        $exist_where[] = ['is_delete', '=', 0];
                        $exist = $model->field($pk)->where($exist_where)->find();
                        if (empty($exist)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $id;
                            $add_temp['menu_name']   = $param['menu_name'] . $v;
                            $add_temp['menu_url']    = $menu_url;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    $model->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                // 提交事务
                $model->commit();
            } catch (\Exception $e) {
                $errmsg = '添加失败：' . $e->getMessage() . ':' . $e->getLine();
                // 回滚事务
                $model->rollback();
            }
            if ($errmsg) {
                exception($errmsg);
            }
        } else {
            $id = $model
                ->strict(false)
                ->insertGetId($param);
            if (empty($id)) {
                exception();
            }
        }

        MenuCache::del();

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 菜单修改
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);
        $info = self::info($id);

        $param['update_time'] = datetime();

        $add = false;
        $add_arr = $edit_arr = ['list' => '列表', 'info' => '信息', 'add' => '添加', 'edit' => '修改', 'dele' => '删除'];
        foreach ($add_arr as $k => $v) {
            $add_key = '';
            $add_key = 'add_' . $k;
            if ($param[$add_key]) {
                $add = true;
            }
        }

        $edit = false;
        foreach ($edit_arr as $k => $v) {
            $edit_key = '';
            $edit_key = 'edit_' . $k;
            if ($param[$edit_key]) {
                $edit = true;
            }
        }

        if ($add || $edit) {
            if (empty($param['menu_url'])) {
                exception('请输入菜单链接：应用/控制器，不含操作');
            }

            $errmsg = '';
            // 启动事务
            $model->startTrans();
            try {
                $model->strict(false)->where($pk, $id)->update($param);

                $edit_data = [];
                foreach ($edit_arr as $k => $v) {
                    $edit_key = '';
                    $edit_key = 'edit_' . $k;
                    if ($param[$edit_key]) {
                        $menu_url =  '';
                        $menu_where = [];
                        $menu_where[] = ['menu_pid', '=', $id];
                        $menu_where[] = ['menu_url', 'like', '%/' . $k];
                        $menu_where[] = ['is_delete', '=', 0];
                        $menu_edit = $model->field($pk)->where($menu_where)->find();
                        if ($menu_edit) {
                            $menu_edit->toArray();
                            $edit_temp = [];
                            $edit_temp['menu_name']   = $param['menu_name'] . $v;
                            $edit_temp['menu_url']    = $param['menu_url'] . '/' . $k;
                            $edit_temp['update_time'] = datetime();
                            $edit_data[] = $edit_temp;
                            $model->where($pk, $menu_edit[$pk])->update($edit_temp);
                        }
                    }
                }
                $param['edit_data'] = $edit_data;
                
                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key]) {
                        $menu_url = '';
                        $menu_url = $param['menu_url'] . '/' . $k;
                        $exist_where = [];
                        $exist_where[] = ['menu_pid', '=', $id];
                        $exist_where[] = ['menu_url', '=', $menu_url];
                        $exist_where[] = ['is_delete', '=', 0];
                        $exist = $model->field($pk)->where($exist_where)->find();
                        if (empty($exist)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $id;
                            $add_temp['menu_name']   = $param['menu_name'] . $v;
                            $add_temp['menu_url']    = $menu_url;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    $model->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                // 提交事务
                $model->commit();
            } catch (\Exception $e) {
                $errmsg = '修改失败：' . $e->getMessage() . ':' . $e->getLine();
                // 回滚事务
                $model->rollback();
            }
            if ($errmsg) {
                exception($errmsg);
            }
        } else {
            $res = $model->strict(false)->where($pk, $id)->update($param);
            if (empty($res)) {
                exception();
            }
        }

        MenuCache::del([$id, $info['menu_url']]);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 菜单删除
     *
     * @param array $ids 菜单id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        foreach ($ids as $v) {
            self::info($v);
        }

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $model = new MenuModel();
        $pk = $model->getPk();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            MenuCache::del([$v, $info['menu_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单设置父级
     *
     * @param array $ids      菜单id
     * @param int   $menu_pid 菜单pid
     * 
     * @return array
     */
    public static function pid($ids, $menu_pid)
    {
        $update['menu_pid']    = $menu_pid;
        $update['update_time'] = datetime();

        $model = new MenuModel();
        $pk = $model->getPk();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            MenuCache::del([$v, $info['menu_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单是否无需登录
     *
     * @param array $ids        菜单id
     * @param int   $is_unlogin 是否无需登录
     * 
     * @return array
     */
    public static function unlogin($ids, $is_unlogin)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['is_unlogin']  = $is_unlogin;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            MenuCache::del([$v, $info['menu_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单是否无需权限
     *
     * @param array $ids       菜单id
     * @param int   $is_unauth 是否无需权限
     * 
     * @return array
     */
    public static function unauth($ids, $is_unauth)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['is_unauth']   = $is_unauth;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            MenuCache::del([$v, $info['menu_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单是否禁用
     *
     * @param array $ids        菜单id
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            $info = self::info($v);
            MenuCache::del([$v, $info['menu_url']]);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单角色
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function role($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return RoleService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 菜单角色解除
     *
     * @param array $param 菜单角色id
     *
     * @return array
     */
    public static function roleRemove($param)
    {
        $model = new MenuModel();
        $pk = $model->getPk();
        $admin_menu_id = $param[$pk];

        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();
        $admin_role_id = $param[$RolePk];

        $admin_role = RoleService::info($admin_role_id);
        $admin_menu_ids = $admin_role['admin_menu_ids'];
        foreach ($admin_menu_ids as $k => $v) {
            if ($admin_menu_id == $v) {
                unset($admin_menu_ids[$k]);
            }
        }
        if (empty($admin_menu_ids)) {
            $admin_menu_ids = str_join('');
        } else {
            $admin_menu_ids = str_join(implode(',', $admin_menu_ids));
        }

        $update['update_time']    = datetime();
        $update['admin_menu_ids'] = $admin_menu_ids;

        $res = $RoleModel->where($RolePk, $admin_role_id)->update($update);
        if (empty($res)) {
            exception();
        }

        RoleCache::del($admin_role_id);

        $update[$pk]     = $admin_menu_id;
        $update[$RolePk] = $admin_role_id;

        return $update;
    }

    /**
     * 菜单用户
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
     * 菜单用户解除
     *
     * @param array $param 菜单用户id
     *
     * @return array
     */
    public static function userRemove($param)
    {
        $model = new MenuModel();
        $pk = $model->getPk();
        $admin_menu_id = $param[$pk];

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        $admin_user_id = $param[$UserPk];

        $admin_user = UserService::info($admin_user_id);
        $admin_menu_ids = $admin_user['admin_menu_ids'];
        foreach ($admin_menu_ids as $k => $v) {
            if ($admin_menu_id == $v) {
                unset($admin_menu_ids[$k]);
            }
        }
        if (empty($admin_menu_ids)) {
            $admin_menu_ids = str_join('');
        } else {
            $admin_menu_ids = str_join(implode(',', $admin_menu_ids));
        }

        $update['update_time']    = datetime();
        $update['admin_menu_ids'] = $admin_menu_ids;

        $res = $UserModel->where($UserPk, $admin_user_id)->update($update);
        if (empty($res)) {
            exception();
        }

        UserCache::upd($admin_user_id);

        $update[$pk]     = $admin_menu_id;
        $update[$UserPk] = $admin_user_id;

        return $update;
    }

    /**
     * 菜单所有子级获取
     *
     * @param array $admin_menu    所有菜单
     * @param int   $admin_menu_id 菜单id
     * 
     * @return array
     */
    public static function getChildren($admin_menu, $admin_menu_id)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $children = [];
        foreach ($admin_menu as $v) {
            if ($v['menu_pid'] == $admin_menu_id) {
                $children[] = $v[$pk];
                $children   = array_merge($children, self::getChildren($admin_menu, $v[$pk]));
            }
        }

        return $children;
    }

    /**
     * 菜单树形获取
     *
     * @param array $admin_menu 所有菜单
     * @param int   $menu_pid   菜单pid
     * 
     * @return array
     */
    public static function toTree($admin_menu, $menu_pid)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $tree = [];
        foreach ($admin_menu as $v) {
            if ($v['menu_pid'] == $menu_pid) {
                $v['children'] = self::toTree($admin_menu, $v[$pk]);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 菜单url列表
     *
     * @return array 
     */
    public static function urlList()
    {
        $urllist_key = 'urlList';
        $urllist     = MenuCache::get($urllist_key);
        if (empty($urllist)) {
            $model = new MenuModel();

            $urllist = $model->where('is_delete', 0)->column('menu_url');
            $urllist = array_filter($urllist);

            MenuCache::set($urllist_key, $urllist);
        }

        return $urllist;
    }

    /**
     * 菜单无需登录url列表
     *
     * @return array
     */
    public static function unloginList()
    {
        $unloginlist_key = 'unloginList';
        $unloginlist     = MenuCache::get($unloginlist_key);
        if (empty($unloginlist)) {
            $model = new MenuModel();

            $unloginlist  = $model->where('is_unlogin', 1)->where('is_delete', 0)->column('menu_url');
            $menu_unlogin = Config::get('admin.menu_is_unlogin');
            $unloginlist  = array_merge($unloginlist, $menu_unlogin);
            $unloginlist  = array_unique(array_filter($unloginlist));

            MenuCache::set($unloginlist_key, $unloginlist);
        }

        return $unloginlist;
    }

    /**
     * 菜单无需权限url列表
     *
     * @return array
     */
    public static function unauthList()
    {
        $unauthlist_key = 'unauthList';
        $unauthlist     = MenuCache::get($unauthlist_key);
        if (empty($unauthlist)) {
            $model = new MenuModel();

            $unauthlist  = $model->where('is_unauth', 1)->where('is_delete', 0)->column('menu_url');
            $menu_unauth = Config::get('admin.menu_is_unauth');
            $unloginlist = self::unloginList();
            $unauthlist  = array_merge($unauthlist, $unloginlist, $menu_unauth);
            $unauthlist  = array_unique(array_filter($unauthlist));

            MenuCache::set($unauthlist_key, $unauthlist);
        }

        return $unauthlist;
    }

    /**
     * 菜单无需限率url列表
     *
     * @return array
     */
    public static function unrateList()
    {
        $unratelist_key = 'unrateList';
        $unratelist     = MenuCache::get($unratelist_key);
        if (empty($unratelist)) {
            $menu_unrate = Config::get('admin.menu_is_unrate');
            $unratelist  = array_unique(array_filter($menu_unrate));

            MenuCache::set($unratelist_key, $unratelist);
        }

        return $unratelist;
    }
}
