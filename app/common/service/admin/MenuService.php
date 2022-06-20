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
            $model = new MenuModel();
            $pk = $model->getPk();

            if (empty($field)) {
                $field = $pk . ',menu_pid,menu_name,menu_type,meta_icon,menu_url,path,name,component,menu_sort,is_unlogin,is_unauth,is_disable,hidden';
            }
            if (empty($order)) {
                $order = ['menu_sort' => 'desc', $pk => 'asc'];
            }

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
            foreach ($data as $k => $v) {
                $data[$k]['children']    = [];
                $data[$k]['hasChildren'] = true;
            }
        } else {
            if (empty($field)) {
                $field = 'admin_menu_id,menu_pid,menu_name,menu_type,meta_icon,menu_url,path,name,component,menu_sort,is_unlogin,is_unauth,is_disable,hidden';
            }

            $key = $type . md5(serialize($where) . $field);
            $data = MenuCache::get($key);
            if (empty($data)) {
                $model = new MenuModel();
                $pk = $model->getPk();

                if (empty($order)) {
                    $order = ['menu_sort' => 'desc', $pk => 'asc'];
                }

                $data = $model->field($field)->where($where)->order($order)->select()->toArray();
                $data = list_to_tree($data, $pk, 'menu_pid');

                MenuCache::set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 菜单信息
     *
     * @param int  $id   菜单id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id = '', $exce = true)
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
                if ($exce) {
                    exception('菜单不存在：' . $id);
                }
                return [];
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
        $add_arr = ['info' => '信息', 'add' => '添加', 'edit' => '修改', 'dele' => '删除'];
        foreach ($add_arr as $k => $v) {
            $add_key = '';
            $add_key = 'add_' . $k;
            if ($param[$add_key] ?? '') {
                $add = true;
            }
        }

        $model = new MenuModel();
        $pk = $model->getPk();

        if ($add) {
            if (empty($param['menu_url'])) {
                exception('请输入菜单链接：应用/控制器/操作');
            }
            $menu_url_pre = substr($param['menu_url'], 0, strripos($param['menu_url'], '/'));

            $errmsg = '';
            // 启动事务
            $model->startTrans();
            try {
                $id = $model->strict(false)->insertGetId($param);

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key] ?? '') {
                        $add_where = [];
                        $add_where[] = ['menu_url', '=', $menu_url_pre . '/' . $k];
                        $add_where[] = ['is_delete', '=', 0];
                        $add_menu = $model->field($pk)->where($add_where)->find();
                        if (empty($add_menu)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $id;
                            $add_temp['menu_type']   = 3;
                            $add_temp['menu_name']   = $param['menu_name'] . $v;
                            $add_temp['menu_url']    = $menu_url_pre . '/' . $k;
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
                $errmsg = '添加失败：' . $e->getMessage();
                // 回滚事务
                $model->rollback();
            }

            if ($errmsg) {
                exception($errmsg);
            }
        } else {
            $id = $model->strict(false)->insertGetId($param);
            if (empty($id)) {
                exception();
            }
        }

        MenuCache::clear();

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

        $param['update_time'] = datetime();

        $add = $edit = false;
        $add_arr = $edit_arr = ['info' => '信息', 'add' => '添加', 'edit' => '修改', 'dele' => '删除'];
        foreach ($add_arr as $k => $v) {
            $add_key = '';
            $add_key = 'add_' . $k;
            if ($param[$add_key] ?? '') {
                $add = true;
            }
        }

        foreach ($edit_arr as $k => $v) {
            $edit_key = '';
            $edit_key = 'edit_' . $k;
            if ($param[$edit_key] ?? '') {
                $edit = true;
            }
        }

        if ($add || $edit) {
            if (empty($param['menu_url'])) {
                exception('请输入菜单链接：应用/控制器/操作');
            }
            $menu_url_pre = substr($param['menu_url'], 0, strripos($param['menu_url'], '/'));

            $errmsg = '';
            // 启动事务
            $model->startTrans();
            try {
                $model->strict(false)->where($pk, $id)->update($param);

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key] ?? '') {
                        $add_where = [];
                        $add_where[] = ['menu_pid', '=', $id];
                        $add_where[] = ['menu_url', '=', $menu_url_pre . '/' . $k];
                        $add_where[] = ['is_delete', '=', 0];
                        $add_menu = $model->field($pk)->where($add_where)->find();
                        if (empty($add_menu)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $id;
                            $add_temp['menu_type']   = 3;
                            $add_temp['menu_name']   = $param['menu_name'] . $v;
                            $add_temp['menu_url']    = $menu_url_pre . '/' . $k;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    $model->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                $edit_data = [];
                foreach ($edit_arr as $k => $v) {
                    $edit_key = '';
                    $edit_key = 'edit_' . $k;
                    if ($param[$edit_key] ?? '') {
                        $edit_where = [];
                        $edit_where[] = ['menu_pid', '=', $id];
                        $edit_where[] = ['menu_url', 'like', '%/' . $k];
                        $edit_where[] = ['is_delete', '=', 0];
                        $edit_menu = $model->field($pk)->where($edit_where)->find();
                        if ($edit_menu) {
                            $edit_menu->toArray();
                            $edit_temp = [];
                            $edit_temp['menu_type']   = 3;
                            $edit_temp['menu_name']   = $param['menu_name'] . $v;
                            $edit_temp['menu_url']    = $menu_url_pre . '/' . $k;
                            $edit_temp['update_time'] = datetime();
                            $edit_data[] = $edit_temp;
                            $model->where($pk, $edit_menu[$pk])->update($edit_temp);
                        }
                    }
                }
                $param['edit_data'] = $edit_data;

                // 提交事务
                $model->commit();
            } catch (\Exception $e) {
                $errmsg = '修改失败：' . $e->getMessage();
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

        $param[$pk] = $id;

        MenuCache::clear();

        return $param;
    }

    /**
     * 菜单删除
     *
     * @param array $ids  菜单id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new MenuModel();
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

        MenuCache::clear();

        return $update;
    }

    /**
     * 菜单修改
     *
     * @param array $ids    菜单id
     * @param array $update 菜单信息
     * 
     * @return array
     */
    public static function update($ids, $update = [])
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        MenuCache::clear();

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
     * @param array $param 菜单id，角色id
     *
     * @return array
     */
    public static function roleRemove($param)
    {
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();
        $admin_menu_id = $param[$MenuPk];

        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();
        $admin_role_id = $param[$RolePk];

        $role = RoleService::info($admin_role_id);
        $admin_menu_ids = $role['admin_menu_ids'];
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

        $update[$MenuPk] = $admin_menu_id;
        $update[$RolePk] = $admin_role_id;

        RoleCache::del($admin_role_id);

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
     * @param array $param 菜单id，用户id
     *
     * @return array
     */
    public static function userRemove($param)
    {
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();
        $admin_menu_id = $param[$MenuPk];

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        $admin_user_id = $param[$UserPk];

        $user = UserService::info($admin_user_id);
        $admin_menu_ids = $user['admin_menu_ids'];
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

        $update[$MenuPk] = $admin_menu_id;
        $update[$UserPk] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 菜单url或id列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array 
     */
    public static function urlList($type = 'url')
    {
        $key = $type;
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $column = 'menu_url';
            if ($type == 'id') {
                $column = $model->getPk();
            }

            $list = $model->where('is_delete', 0)->column($column);
            $list = array_filter($list);

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免登url或id列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array
     */
    public static function unloginUrl($type = 'url')
    {
        $key = 'unlogin-' . $type;
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $column = 'menu_url';
            $menu_is_unlogin = Config::get('admin.menu_is_unlogin', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($menu_is_unlogin) {
                    $menu_is_unlogin = $model->where('menu_url', 'in', $menu_is_unlogin)->column($column);
                }
            }

            $list = $model->where('is_unlogin', 1)->where('is_delete', 0)->column($column);
            $list = array_merge($list, $menu_is_unlogin);
            $list = array_unique(array_filter($list));

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免权url或id列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array
     */
    public static function unauthUrl($type = 'url')
    {
        $key = 'unauth-' . $type;
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $column = 'menu_url';
            $menu_is_unauth = Config::get('admin.menu_is_unauth', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($menu_is_unauth) {
                    $menu_is_unauth = $model->where('menu_url', 'in', $menu_is_unauth)->column($column);
                }
            }
            $menu_is_unlogin = self::unloginUrl($type);

            $list = $model->where('is_unauth', 1)->where('is_delete', 0)->column($column);
            $list = array_merge($list, $menu_is_unlogin, $menu_is_unauth);
            $list = array_unique(array_filter($list));

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单无需限率url或id列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array
     */
    public static function unrateUrl($type = 'url')
    {
        $key = 'unrate-' . $type;
        $list = MenuCache::get($key);
        if (empty($list)) {
            $menu_is_unrate = Config::get('admin.menu_is_unrate', []);
            if ($type == 'id') {
                $model = new MenuModel();
                $column = $model->getPk();
                if ($menu_is_unrate) {
                    $menu_is_unrate = $model->where('menu_url', 'in', $menu_is_unrate)->column($column);
                }
            }

            $list = $menu_is_unrate;
            $list = array_unique(array_filter($list));

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单路由
     *
     * @param array   $ids   菜单id
     * @param string  $pk    主键名称
     * @param string  $pid   父键名称
     * @param integer $root  根节点id
     * @param string  $child 子节点名称
     *
     * @return array
     */
    public static function menus($ids = [], $pk = 'admin_menu_id', $pid = 'menu_pid', $root = 0,  $child = 'children')
    {
        $tree = $refer = $list = [];
        $where = [['admin_menu_id', 'in', $ids]];
        $field = 'admin_menu_id,menu_pid,menu_name,menu_type,path,name,component,meta_icon,meta_query,is_disable,hidden';
        $menu = self::list('list', $where, [], $field);

        foreach ($menu as $v) {
            if ($v['is_disable'] == 0 && $v['menu_type'] != 3) {
                $tmp = [];
                $tmp['admin_menu_id'] = $v['admin_menu_id'];
                $tmp['menu_pid'] = $v['menu_pid'];
                $tmp['path'] = $v['path'];
                $tmp['name'] = $v['name'];
                $tmp['meta']['title'] = $v['menu_name'];
                $tmp['meta']['icon'] = $v['meta_icon'];
                if ($v['menu_type'] == 1) {
                    $tmp['redirect'] = 'noRedirect';
                    $tmp['component'] = 'Layout';
                    $tmp['alwaysShow'] = true;
                } elseif ($v['menu_type'] == 2) {
                    $tmp['meta']['query'] = $v['meta_query'] ? json_decode($v['meta_query'], true) : [];
                    $tmp['component'] = $v['component'];
                    // 外链
                    if (strpos($v['path'], 'http') === 0) {
                        unset($tmp['name']);
                    }
                }
                $tmp['hidden'] = $v['hidden'] ? true : false;
                $list[] = $tmp;
            }
        }

        foreach ($list as $kl => $vl) {
            $refer[$vl[$pk]] = &$list[$kl];
        }

        foreach ($list as $key => $val) {
            $parent_id = 0;
            if (isset($val[$pid])) {
                $parent_id = $val[$pid];
            }
            if ($root == $parent_id) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parent_id])) {
                    $parent = &$refer[$parent_id];
                    $parent[$child][] = &$list[$key];
                }
            }
        }

        return $tree;
    }
}
