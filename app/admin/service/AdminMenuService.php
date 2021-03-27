<?php
/*
 * @Description  : 菜单管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-25
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\AdminMenuCache;
use app\common\cache\AdminRoleCache;
use app\common\cache\AdminAdminCache;

class AdminMenuService
{
    /**
     * 菜单列表
     * 
     * @param string $type list列表，tree树形，url链接
     *
     * @return array 
     */
    public static function list($type = 'tree')
    {

        $menu = AdminMenuCache::get();

        if (empty($menu)) {
            $field = 'admin_menu_id,menu_pid,menu_name,menu_url,menu_sort,is_disable,is_unauth,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['menu_sort' => 'desc', 'admin_menu_id' => 'asc'];

            $list = Db::name('admin_menu')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $tree = self::toTree($list, 0);
            $url  = array_filter(array_column($list, 'menu_url'));

            sort($url);

            $menu['tree'] = $tree;
            $menu['list'] = $list;
            $menu['url']  = $url;

            AdminMenuCache::set('', $menu);
        }

        if ($type == 'list') {
            $data['count'] = count($menu['list']);
            $data['list']  = $menu['list'];
        } elseif ($type == 'url') {
            $data['count'] = count($menu['url']);
            $data['list']  = $menu['url'];
        } else {
            $data['count'] = count($menu['tree']);
            $data['list']  = $menu['tree'];
        }

        return $data;
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
            $admin_menu_id = request_pathinfo();
        }
        
        $admin_menu = AdminMenuCache::get($admin_menu_id);

        if (empty($admin_menu)) {
            if (is_numeric($admin_menu_id)) {
                $where[] = ['admin_menu_id', '=',  $admin_menu_id];
            } else {
                $where[] = ['is_delete', '=', 0];
                $where[] = ['menu_url', '=',  $admin_menu_id];
            }

            $admin_menu = Db::name('admin_menu')
                ->where($where)
                ->find();

            if (empty($admin_menu)) {
                exception('菜单不存在：' . $admin_menu_id);
            }

            AdminMenuCache::set($admin_menu_id, $admin_menu);
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

        $admin_menu_id = Db::name('admin_menu')
            ->insertGetId($param);

        if (empty($admin_menu_id)) {
            exception();
        }

        $param['admin_menu_id'] = $admin_menu_id;

        AdminMenuCache::del();

        return $param;
    }

    /**
     * 菜单修改
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $admin_menu_id = $param['admin_menu_id'];
        $admin_menu    = self::info($admin_menu_id);

        if ($method == 'get') {
            return $admin_menu;
        } else {
            unset($param['admin_menu_id']);

            $param['update_time'] = datetime();

            $res = Db::name('admin_menu')
                ->where('admin_menu_id', '=', $admin_menu_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['admin_menu_id'] = $admin_menu_id;

            AdminMenuCache::del();
            AdminMenuCache::del($admin_menu_id);
            AdminMenuCache::del($admin_menu['menu_url']);

            return $param;
        }
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

        $res = Db::name('admin_menu')
            ->where('admin_menu_id', '=', $admin_menu_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_menu_id'] = $admin_menu_id;

        AdminMenuCache::del();
        AdminMenuCache::del($admin_menu_id);
        AdminMenuCache::del($admin_menu['menu_url']);

        return $update;
    }

    /**
     * 菜单上传图片
     *
     * @param array $param 图片信息
     * 
     * @return array
     */
    public static function upload($param)
    {
        $image_field = $param['image_field'];
        $image_file  = $param['image_file'];

        $image_file_name = Filesystem::disk('public')
            ->putFile('admin_menu', $image_file, function () use ($image_field) {
                return date('Ymd') . '/' . date('YmdHis') . '_' . $image_field;
            });

        $update['image']       = 'storage/' . $image_file_name;
        $update['image_src']   = file_url($update['image']);
        $update['image_field'] = $image_field;

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
        $admin_menu_id = $param['admin_menu_id'];

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = datetime();

        $res = Db::name('admin_menu')
            ->where('admin_menu_id', $admin_menu_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $admin_menu = self::info($admin_menu_id);

        $update['admin_menu_id'] = $admin_menu_id;

        AdminMenuCache::del();
        AdminMenuCache::del($admin_menu_id);
        AdminMenuCache::del($admin_menu['menu_url']);

        return $update;
    }

    /**
     * 菜单是否无需授权
     *
     * @param array $param 菜单信息
     * 
     * @return array
     */
    public static function unauth($param)
    {
        $admin_menu_id = $param['admin_menu_id'];

        $update['is_unauth']   = $param['is_unauth'];
        $update['update_time'] = datetime();

        $res = Db::name('admin_menu')
            ->where('admin_menu_id', $admin_menu_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $admin_menu = self::info($admin_menu_id);

        $update['admin_menu_id'] = $admin_menu_id;

        AdminMenuCache::del();
        AdminMenuCache::del($admin_menu_id);
        AdminMenuCache::del($admin_menu['menu_url']);

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
        $data = AdminRoleService::list($where, $page, $limit, $order, $field);

        return $data;
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
        $admin_menu_id = $param['admin_menu_id'];
        $admin_role_id = $param['admin_role_id'];

        $admin_role = AdminRoleService::info($admin_role_id);

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

        $res = Db::name('admin_role')
            ->where('admin_role_id', $admin_role_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_menu_id'] = $admin_menu_id;
        $update['admin_role_id'] = $admin_role_id;

        AdminRoleCache::del($admin_role_id);

        return $update;
    }

    /**
     * 菜单管理员
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     *
     * @return array 
     */
    public static function admin($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $data = AdminAdminService::list($where, $page, $limit, $order, $field);

        return $data;
    }

    /**
     * 菜单管理员解除
     *
     * @param array $param 菜单管理员id
     *
     * @return array
     */
    public static function adminRemove($param)
    {
        $admin_menu_id  = $param['admin_menu_id'];
        $admin_admin_id = $param['admin_admin_id'];

        $admin_admin = AdminAdminService::info($admin_admin_id);

        $admin_menu_ids = $admin_admin['admin_menu_ids'];
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

        $res = Db::name('admin_admin')
            ->where('admin_admin_id', $admin_admin_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_menu_id']  = $admin_menu_id;
        $update['admin_admin_id'] = $admin_admin_id;

        AdminAdminCache::upd($admin_admin_id);

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
                $children[] = $v['admin_menu_id'];
                $children   = array_merge($children, self::getChildren($admin_menu, $v['admin_menu_id']));
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
                $v['children'] = self::toTree($admin_menu, $v['admin_menu_id']);
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
        $data = Db::name('admin_menu')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
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
    public static function etQuery($keyword, $field = 'menu_url|menu_name')
    {
        $data = Db::name('admin_menu')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $data;
    }
}
