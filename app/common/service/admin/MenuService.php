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

use think\facade\Db;
use think\facade\Config;
use app\common\cache\admin\MenuCache;
use app\common\cache\admin\RoleCache;
use app\common\cache\admin\UserCache;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class MenuService
{
    // 表名
    protected static $t_name = 'admin_menu';
    // 表主键
    protected static $t_pk = 'admin_menu_id';

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
            $field = self::$t_pk . ',menu_pid,menu_name,menu_url,is_unauth,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['menu_sort' => 'desc', self::$t_pk => 'asc'];

            $list = Db::name(self::$t_name)
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

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
            $field = self::$t_pk . ',menu_pid,menu_name,menu_url,menu_sort,is_disable,is_unauth,is_unlogin';

            $where[] = ['is_delete', '=', 0];

            $order = ['menu_sort' => 'desc', self::$t_pk => 'asc'];

            $list = Db::name(self::$t_name)
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $tree = self::toTree($list, 0);

            MenuCache::set($key, $tree);
        }

        return $tree;
    }

    /**
     * 菜单信息
     *
     * @param integer $admin_menu_id 菜单id
     * 
     * @return array
     */
    public static function info($admin_menu_id = '')
    {
        if (empty($admin_menu_id)) {
            $admin_menu_id = menu_url();
        }

        $admin_menu = MenuCache::get($admin_menu_id);
        if (empty($admin_menu)) {
            if (is_numeric($admin_menu_id)) {
                $where[] = [self::$t_pk, '=',  $admin_menu_id];
            } else {
                $where[] = ['menu_url', '=',  $admin_menu_id];
                $where[] = ['is_delete', '=', 0];
            }

            $admin_menu = Db::name(self::$t_name)
                ->where($where)
                ->find();
            if (empty($admin_menu)) {
                exception('菜单不存在：' . $admin_menu_id);
            }

            MenuCache::set($admin_menu_id, $admin_menu);
        }

        return $admin_menu;
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

        if ($add) {
            if (empty($param['menu_url'])) {
                exception('请输入菜单链接：应用/控制器，不含操作');
            }

            $errmsg = '';
            // 启动事务
            Db::startTrans();
            try {
                $admin_menu_id = Db::name(self::$t_name)
                    ->strict(false)
                    ->insertGetId($param);

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = '';
                    $add_key = 'add_' . $k;
                    if ($param[$add_key]) {
                        $menu_url = '';
                        $menu_url = $param['menu_url'] . '/' . $k;
                        $exist = Db::name(self::$t_name)
                            ->field(self::$t_pk)
                            ->where('menu_url', '=', $menu_url)
                            ->where('is_delete', '=', 0)
                            ->find();
                        if (empty($exist)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $admin_menu_id;
                            $add_temp['menu_name']   = $param['menu_name'] . $v;
                            $add_temp['menu_url']    = $menu_url;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    Db::name(self::$t_name)
                        ->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                $errmsg = '添加失败：' . $e->getMessage() . ':' . $e->getLine();
                // 回滚事务
                Db::rollback();
            }
            if ($errmsg) {
                exception($errmsg);
            }
        } else {
            $admin_menu_id = Db::name(self::$t_name)
                ->strict(false)
                ->insertGetId($param);
            if (empty($admin_menu_id)) {
                exception();
            }
        }

        $param[self::$t_pk] = $admin_menu_id;

        MenuCache::del();

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
        $admin_menu_id = $param[self::$t_pk];
        $admin_menu    = self::info($admin_menu_id);

        unset($param[self::$t_pk]);

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
            Db::startTrans();
            try {
                Db::name(self::$t_name)
                    ->strict(false)
                    ->where(self::$t_pk, '=', $admin_menu_id)
                    ->update($param);

                $edit_data = [];
                foreach ($edit_arr as $k => $v) {
                    $edit_key = '';
                    $edit_key = 'edit_' . $k;
                    if ($param[$edit_key]) {
                        $menu_url = '';
                        $menu_url = $param['menu_url'] . '/' . $k;
                        $admin_menu_edit = Db::name(self::$t_name)
                            ->field(self::$t_pk)
                            ->where('menu_pid', '=', $admin_menu_id)
                            ->where('menu_url', '=', $menu_url)
                            ->where('is_delete', '=', 0)
                            ->find();
                        if ($admin_menu_edit) {
                            $edit_temp = [];
                            $edit_temp['menu_name']   = $param['menu_name'] . $v;
                            $edit_temp['menu_url']    = $param['menu_url'] . '/' . $k;
                            $edit_temp['update_time'] = datetime();
                            $edit_data[] = $edit_temp;
                            Db::name(self::$t_name)
                                ->where(self::$t_pk, $admin_menu_edit[self::$t_pk])
                                ->update($edit_temp);
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
                        $exist = Db::name(self::$t_name)
                            ->field(self::$t_pk)
                            ->where('menu_pid', '=', $admin_menu_id)
                            ->where('menu_url', '=', $menu_url)
                            ->where('is_delete', '=', 0)
                            ->find();
                        if (empty($exist)) {
                            $add_temp = [];
                            $add_temp['menu_pid']    = $admin_menu_id;
                            $add_temp['menu_name']   = $param['menu_name'] . $v;
                            $add_temp['menu_url']    = $menu_url;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    Db::name(self::$t_name)
                        ->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                $errmsg = '修改失败：' . $e->getMessage() . ':' . $e->getLine();
                // 回滚事务
                Db::rollback();
            }
            if ($errmsg) {
                exception($errmsg);
            }
        } else {
            $res = Db::name(self::$t_name)
                ->strict(false)
                ->where(self::$t_pk, '=', $admin_menu_id)
                ->update($param);
            if (empty($res)) {
                exception();
            }
        }

        $param[self::$t_pk] = $admin_menu_id;

        MenuCache::del([$admin_menu_id, $admin_menu['menu_url']]);

        return $param;
    }

    /**
     * 菜单删除
     *
     * @param integer $admin_menu_id 菜单id
     * 
     * @return array
     */
    public static function dele($admin_menu_id)
    {
        $admin_menu = self::info($admin_menu_id);

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, '=', $admin_menu_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $admin_menu_id;

        MenuCache::del([$admin_menu_id, $admin_menu['menu_url']]);

        return $update;
    }

    /**
     * 菜单是否禁用
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function disable($param)
    {
        $admin_menu_id = $param[self::$t_pk];

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_menu_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $admin_menu = self::info($admin_menu_id);

        $update[self::$t_pk] = $admin_menu_id;

        MenuCache::del([$admin_menu_id, $admin_menu['menu_url']]);

        return $update;
    }

    /**
     * 菜单是否无需权限
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function unauth($param)
    {
        $admin_menu_id = $param[self::$t_pk];

        $update['is_unauth']   = $param['is_unauth'];
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_menu_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $admin_menu = self::info($admin_menu_id);

        $update[self::$t_pk] = $admin_menu_id;

        MenuCache::del([$admin_menu_id, $admin_menu['menu_url']]);

        return $update;
    }

    /**
     * 菜单是否无需登录
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function unlogin($param)
    {
        $admin_menu_id = $param[self::$t_pk];

        $update['is_unlogin']  = $param['is_unlogin'];
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_menu_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $admin_menu = self::info($admin_menu_id);

        $update[self::$t_pk] = $admin_menu_id;

        MenuCache::del([$admin_menu_id, $admin_menu['menu_url']]);

        return $update;
    }

    /**
     * 菜单角色
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
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
        $admin_menu_id = $param[self::$t_pk];
        $admin_role_id = $param['admin_role_id'];

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

        $AdminRole = new RoleModel();
        $res = $AdminRole
            ->where('admin_role_id', $admin_role_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $admin_menu_id;
        $update['admin_role_id'] = $admin_role_id;

        RoleCache::del($admin_role_id);

        return $update;
    }

    /**
     * 菜单用户
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
     * 菜单用户解除
     *
     * @param array $param 菜单用户id
     *
     * @return array
     */
    public static function userRemove($param)
    {
        $admin_menu_id = $param[self::$t_pk];
        $admin_user_id = $param['admin_user_id'];

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

        $AdminUser = new UserModel();
        $res = $AdminUser
            ->where('admin_user_id', $admin_user_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $admin_menu_id;
        $update['admin_user_id'] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 菜单所有子级获取
     *
     * @param array   $admin_menu    所有菜单
     * @param integer $admin_menu_id 菜单id
     * 
     * @return array
     */
    public static function getChildren($admin_menu, $admin_menu_id)
    {
        $children = [];

        foreach ($admin_menu as $k => $v) {
            if ($v['menu_pid'] == $admin_menu_id) {
                $children[] = $v[self::$t_pk];
                $children   = array_merge($children, self::getChildren($admin_menu, $v[self::$t_pk]));
            }
        }

        return $children;
    }

    /**
     * 菜单树形获取
     *
     * @param array   $admin_menu 所有菜单
     * @param integer $menu_pid   菜单父级id
     * 
     * @return array
     */
    public static function toTree($admin_menu, $menu_pid)
    {
        $tree = [];

        foreach ($admin_menu as $k => $v) {
            if ($v['menu_pid'] == $menu_pid) {
                $v['children'] = self::toTree($admin_menu, $v[self::$t_pk]);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 菜单模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'menu_url|menu_name')
    {
        $data = Db::name(self::$t_name)
            ->where($field, 'like', '%' . $keyword . '%')
            ->where('is_delete', '=', 0)
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 菜单精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function equQuery($keyword, $field = 'menu_url|menu_name')
    {
        $data = Db::name(self::$t_name)
            ->where($field, '=', $keyword)
            ->where('is_delete', '=', 0)
            ->select()
            ->toArray();

        return $data;
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
            $urllist = Db::name(self::$t_name)
                ->field('menu_url')
                ->where('is_delete', '=', 0)
                ->column('menu_url');

            $urllist = array_filter($urllist);

            MenuCache::set($urllist_key, $urllist);
        }

        return $urllist;
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
            $unauthlist = Db::name(self::$t_name)
                ->field('menu_url')
                ->where('is_unauth', '=', 1)
                ->where('is_delete', '=', 0)
                ->column('menu_url');

            $menu_unauth = Config::get('admin.menu_is_unauth');
            $unloginlist = self::unloginList();
            $unauthlist  = array_merge($unauthlist, $unloginlist, $menu_unauth);
            $unauthlist  = array_unique(array_filter($unauthlist));

            MenuCache::set($unauthlist_key, $unauthlist);
        }

        return $unauthlist;
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
            $unloginlist = Db::name(self::$t_name)
                ->field('menu_url')
                ->where('is_unlogin', '=', 1)
                ->where('is_delete', '=', 0)
                ->column('menu_url');

            $menu_unlogin = Config::get('admin.menu_is_unlogin');
            $unloginlist  = array_merge($unloginlist, $menu_unlogin);
            $unloginlist  = array_unique(array_filter($unloginlist));

            MenuCache::set($unloginlist_key, $unloginlist);
        }

        return $unloginlist;
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
