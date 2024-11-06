<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use think\facade\Config;
use app\common\cache\system\MenuCache;
use app\common\cache\system\RoleCache;
use app\common\cache\system\UserCache;
use app\common\model\system\MenuModel;
use app\common\model\system\UserModel;
use app\common\model\system\RoleMenusModel;

/**
 * 菜单管理
 */
class MenuService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'menu_id/d'      => '',
        'menu_pid/d'     => 0,
        'menu_type/d'    => SettingService::MENU_TYPE_CATALOGUE,
        'meta_icon/s'    => '',
        'menu_name/s'    => '',
        'menu_url/s'     => '',
        'path/s'         => '',
        'component/s'    => '',
        'name/s'         => '',
        'meta_query/s'   => '',
        'hidden/d'       => 0,
        'keep_alive/d'   => 1,
        'always_show/d'  => 0,
        'sort/d'         => 250,
        'add_info/b'     => false,
        'add_add/b'      => false,
        'add_edit/b'     => false,
        'add_dele/b'     => false,
        'add_disable/b'  => false,
        'edit_info/b'    => false,
        'edit_add/b'     => false,
        'edit_edit/b'    => false,
        'edit_dele/b'    => false,
        'edit_disable/b' => false,
    ];

    /**
     * 菜单列表
     *
     * @param string $type  tree树形，list列表
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array []
     */
    public static function list($type = 'tree', $where = [], $order = [], $field = '')
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',menu_pid,menu_name,menu_type,meta_icon,menu_url,path,name,component,hidden,sort,is_unlogin,is_unauth,is_unrate,is_disable';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'asc'];
        }

        $key = where_cache_key($type, $where, $order, $field);
        $data = MenuCache::get($key);
        if (empty($data)) {
            $append = [];
            if (strpos($field, 'menu_type')) {
                $append[] = 'menu_type_name';
            }
            if (strpos($field, 'is_unlogin')) {
                $append[] = 'is_unlogin_name';
            }
            if (strpos($field, 'is_unauth')) {
                $append[] = 'is_unauth_name';
            }
            if (strpos($field, 'is_unrate')) {
                $append[] = 'is_unrate_name';
            }
            if (strpos($field, 'is_disable')) {
                $append[] = 'is_disable_name';
            }
            if (strpos($field, 'hidden')) {
                $append[] = 'hidden_name';
            }
            $data = $model->field($field)->append($append)->where($where)->order($order)->select()->toArray();
            if ($type == 'tree') {
                $data = array_to_tree($data, $pk, 'menu_pid');
            }
            MenuCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 菜单信息
     *
     * @param int|string $id   菜单id、url
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
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
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['menu_url', '=', $id];
                $where[] = where_delete();
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
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $add = false;
        $add_arr = ['info' => '信息', 'add' => '添加', 'edit' => '修改', 'dele' => '删除', 'disable' => '是否禁用'];
        foreach ($add_arr as $k => $v) {
            $add_key = '';
            $add_key = 'add_' . $k;
            if ($param[$add_key] ?? '') {
                $add = true;
            }
        }

        // 启动事务
        $model->startTrans();
        try {
            $menu = MenuModel::create($param);
            $id = $menu->$pk;

            if ($add) {
                if (empty($param['menu_url'])) {
                    exception('请输入菜单链接：应用/控制器/操作');
                }
                $menu_url_pre = substr($param['menu_url'], 0, strripos($param['menu_url'], '/'));

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key] ?? '') {
                        $add_where = [];
                        $add_where[] = where_delete();
                        $add_where[] = ['menu_url', '=', $menu_url_pre . '/' . $k];
                        $add_menu = $model->field($pk)->where($add_where)->find();
                        if (empty($add_menu)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $id;
                            $add_temp['menu_type']   = SettingService::MENU_TYPE_BUTTON;
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

        $param[$pk] = $id;

        MenuCache::clear();

        return $param;
    }

    /**
     * 菜单修改
     *
     * @param int   $id    菜单id
     * @param array $param 菜单信息
     * 
     * @return array|Exception
     */
    public static function edit($id, $param)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $add = $edit = false;
        $add_arr = $edit_arr = ['info' => '信息', 'add' => '添加', 'edit' => '修改', 'dele' => '删除', 'disable' => '是否禁用'];
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

        // 启动事务
        $model->startTrans();
        try {
            $menu = $model->find($id);
            $menu->save($param);

            if ($add || $edit) {
                if (empty($param['menu_url'])) {
                    exception('请输入菜单链接：应用/控制器/操作');
                }
                $menu_url_pre = substr($param['menu_url'], 0, strripos($param['menu_url'], '/'));

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key] ?? '') {
                        $add_where = [];
                        $add_where[] = where_delete();
                        $add_where[] = ['menu_pid', '=', $id];
                        $add_where[] = ['menu_url', '=', $menu_url_pre . '/' . $k];
                        $add_menu = $model->field($pk)->where($add_where)->find();
                        if (empty($add_menu)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $id;
                            $add_temp['menu_type']   = SettingService::MENU_TYPE_BUTTON;
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
                        $edit_where[] = where_delete();
                        $edit_where[] = ['menu_pid', '=', $id];
                        $edit_where[] = ['menu_url', 'like', '%/' . $k];
                        $edit_menu = $model->field($pk)->where($edit_where)->find();
                        if ($edit_menu) {
                            $edit_temp = [];
                            $edit_temp['menu_type']   = SettingService::MENU_TYPE_BUTTON;
                            $edit_temp['menu_name']   = $param['menu_name'] . $v;
                            $edit_temp['menu_url']    = $menu_url_pre . '/' . $k;
                            $edit_temp['update_time'] = datetime();
                            $edit_data[] = $edit_temp;
                            $edit_menu->save($edit_temp);
                        }
                    }
                }
                $param['edit_data'] = $edit_data;
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
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new MenuModel();
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

        MenuCache::clear();

        return $update;
    }

    /**
     * 菜单更新
     *
     * @param array $ids   菜单id
     * @param array $param 菜单信息
     * 
     * @return array|Exception
     */
    public static function update($ids, $param = [])
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        MenuCache::clear();

        return $param;
    }

    /**
     * 菜单角色列表
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
     * @param array $menu_id  菜单id
     * @param array $role_ids 角色id
     *
     * @return int
     */
    public static function roleRemove($menu_id, $role_ids = [])
    {
        $where[] = ['menu_id', 'in', $menu_id];
        if (empty($role_ids)) {
            $role_ids = RoleMenusModel::where($where)->column('role_id');
        }
        $where[] = ['role_id', 'in', $role_ids];

        $res = RoleMenusModel::where($where)->delete();
        $user_ids = UserModel::select()->column('user_id');

        RoleCache::del($role_ids);
        UserCache::del($user_ids);

        return $res;
    }

    /**
     * 菜单列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array 
     */
    public static function menuList($type = 'url')
    {
        $key = $type;
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $column = 'menu_url';
            if ($type == 'id') {
                $column = $model->getPk();
            }

            $list = $model->where([where_delete()])->column($column);
            $list = array_filter($list);
            $list = array_values($list);
            sort($list);

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免登列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array
     */
    public static function unloginList($type = 'url')
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

            $list = $model->where(where_delete(['is_unlogin', '=', 1]))->column($column);
            $list = array_merge($list, $menu_is_unlogin);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免权列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array
     */
    public static function unauthList($type = 'url')
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
            $menu_is_unlogin = self::unloginList($type);

            $list = $model->where(where_delete(['is_unauth', '=', 1]))->column($column);
            $list = array_merge($list, $menu_is_unlogin, $menu_is_unauth);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免限列表
     * 
     * @param string $type url菜单url，id菜单id
     *
     * @return array
     */
    public static function unrateList($type = 'url')
    {
        $key = 'unrate-' . $type;
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $column = 'menu_url';
            $menu_is_unrate = Config::get('admin.menu_is_unrate', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($menu_is_unrate) {
                    $menu_is_unrate = $model->where('menu_url', 'in', $menu_is_unrate)->column($column);
                }
            }

            $list = $model->where(where_delete(['is_unrate', '=', 1]))->column($column);
            $list = array_merge($list, $menu_is_unrate);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单路由
     *
     * @param array $ids 菜单id
     *
     * @return array
     */
    public static function menus($ids = [])
    {
        $where = where_delete(['menu_id', 'in', $ids]);
        $field = 'menu_id,menu_pid,menu_name,menu_type,path,name,component,meta_icon,meta_query,hidden,keep_alive,always_show,is_disable';
        $menu  = self::list('list', $where, [], $field);
        $list  = [];
        foreach ($menu as $v) {
            if ($v['menu_type'] != SettingService::MENU_TYPE_BUTTON) {
                $tmp = [];
                $tmp['menu_id']  = $v['menu_id'];
                $tmp['menu_pid'] = $v['menu_pid'];
                $tmp['path'] = $v['path'];
                $tmp['name'] = $v['name'];
                $tmp['meta']['title'] = $v['menu_name'];
                $tmp['meta']['icon']  = $v['meta_icon'];
                $tmp['meta']['hidden'] = $v['hidden'] ? true : false;
                $tmp['meta']['keepAlive'] = $v['keep_alive'] ? true : false;
                $tmp['meta']['alwaysShow'] = $v['always_show'] ? true : false;
                if ($v['menu_type'] == SettingService::MENU_TYPE_CATALOGUE) {
                    $tmp['redirect']  = 'noRedirect';
                    $tmp['component'] = 'Layout';
                    if ($v['menu_pid'] > 0) {
                        $tmp['redirect']  = $v['component'];
                        $tmp['component'] = $v['component'];
                    }
                } elseif ($v['menu_type'] == SettingService::MENU_TYPE_MENU) {
                    $tmp['meta']['query'] = $v['meta_query'] ? json_decode($v['meta_query'], true) : [];
                    $tmp['component'] = $v['component'];
                    // 外链
                    if (strpos($v['path'], 'http') === 0) {
                        unset($tmp['name']);
                    }
                }
                $list[] = $tmp;
            }
        }

        return list_to_tree($list, 'menu_id', 'menu_pid');
    }
}
