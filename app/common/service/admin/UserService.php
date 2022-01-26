<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户管理
namespace app\common\service\admin;

use think\facade\Config;
use app\common\utils\IpInfoUtils;
use app\common\cache\admin\UserCache;
use app\common\model\admin\MenuModel;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;
use app\common\service\file\FileService;
use app\common\service\admin\TokenService;

class UserService
{
    /**
     * 用户列表
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
        $model = new UserModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',username,nickname,phone,email,sort,is_disable,is_super,login_num,create_time,login_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 用户信息
     *
     * @param int  $id   用户id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = UserCache::get($id);
        if (empty($info)) {
            $model = new UserModel();
            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('用户不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $MenuModel = new MenuModel();
            $MenuPk = $MenuModel->getPk();

            $info['avatar_url']     = FileService::fileUrl($info['avatar_id']);
            $info['admin_role_ids'] = str_trim($info['admin_role_ids']);
            $info['admin_menu_ids'] = str_trim($info['admin_menu_ids']);
            if (admin_is_super($id)) {
                $menu = $MenuModel->field($MenuPk . ',menu_url')->where('is_delete', 0)->select()->toArray();
                $menu_ids = array_column($menu, 'admin_menu_id');
                $menu_url = array_column($menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } elseif ($info['is_super'] == 1) {
                $menu = $MenuModel->field($MenuPk . ',menu_url')->where('is_disable', 0)->where('is_delete', 0)->select()->toArray();
                $menu_ids = array_column($menu, 'admin_menu_id');
                $menu_url = array_column($menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } else {
                $RoleModel = new RoleModel();
                $RolePk = $RoleModel->getPk();

                $menu_where[] = [$RolePk, 'in', $info['admin_role_ids']];
                $menu_where[] = ['is_disable', '=', 0];
                $menu_where[] = ['is_delete', '=', 0];
                $menu_ids = $RoleModel->where($menu_where)->column('admin_menu_ids');

                $menu_ids[]   = $info['admin_menu_ids'];
                $menu_ids_str = implode(',', $menu_ids);
                $menu_ids_arr = explode(',', $menu_ids_str);
                $menu_ids     = array_unique($menu_ids_arr);
                $menu_ids     = array_filter($menu_ids);

                $where[] = ['admin_menu_id', 'in', $menu_ids];
                $where[] = ['menu_url', '<>', ''];
                $where[] = ['is_disable', '=', 0];
                $where[] = ['is_delete', '=', 0];

                $where_un[] = ['menu_url', '<>', ''];
                $where_un[] = ['is_unauth', '=', 1];
                $where_un[] = ['is_disable', '=', 0];
                $where_un[] = ['is_delete', '=', 0];

                $menu_url = $MenuModel->whereOr([$where, $where_un])->column('menu_url');
            }

            $admin_role_ids = $info['admin_role_ids'];
            if (empty($admin_role_ids)) {
                $admin_role_ids = [];
            } else {
                $admin_role_ids = explode(',', $info['admin_role_ids']);
                foreach ($admin_role_ids as $k => $v) {
                    $admin_role_ids[$k] = (int) $v;
                }
            }

            $admin_menu_ids = $info['admin_menu_ids'];
            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            } else {
                $admin_menu_ids = explode(',', $info['admin_menu_ids']);
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
            }

            if (empty($menu_ids)) {
                $menu_ids = [];
            } else {
                foreach ($menu_ids as $k => $v) {
                    $menu_ids[$k] = (int) $v;
                }
            }

            $menu_is_unlogin = Config::get('admin.menu_is_unlogin', []);
            $menu_is_unauth  = Config::get('admin.menu_is_unauth', []);
            $unlogin_unauth  = array_merge($menu_is_unlogin, $menu_is_unauth);
            $menu_url        = array_merge($menu_url, $unlogin_unauth);
            $menu_url        = array_unique($menu_url);

            sort($menu_url);

            $info['admin_token']    = TokenService::create($info);
            $info['admin_role_ids'] = $admin_role_ids;
            $info['admin_menu_ids'] = $admin_menu_ids;
            $info['menu_ids']       = $menu_ids;
            $info['roles']          = $menu_url;

            UserCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 用户添加
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $param['password']    = md5($param['password']);
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 用户修改
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        UserCache::upd($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 用户删除
     *
     * @param array $ids 用户id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户分配权限
     *
     * @param array  $param  用户信息
     * @param string $method 请求方式
     * 
     * @return array
     */
    public static function rule($param, $method = 'get')
    {
        $model = new UserModel();
        $pk = $model->getPk();

        if ($method == 'get') {
            $RoleModel = new RoleModel();
            $RolePk = $RoleModel->getPk();

            $admin_user_id = $param[$pk];
            $admin_menu    = MenuService::list();
            $admin_role    = $RoleModel->field($RolePk . ',role_name')->where('is_delete', 0)->select()->toArray();
            $admin_user    = UserService::info($admin_user_id);

            $menu_ids       = $admin_user['menu_ids'];
            $admin_menu_ids = $admin_user['admin_menu_ids'];
            $role_menu_ids  = RoleService::getMenuId($admin_user['admin_role_ids']);

            foreach ($admin_menu as $k => $v) {
                $admin_menu[$k]['is_check'] = 0;
                $admin_menu[$k]['is_role']  = 0;
                $admin_menu[$k]['is_menu']  = 0;
                foreach ($menu_ids as $vmis) {
                    if ($v['admin_menu_id'] == $vmis) {
                        $admin_menu[$k]['is_check'] = 1;
                    }
                }
                foreach ($admin_menu_ids as $vami) {
                    if ($v['admin_menu_id'] == $vami) {
                        $admin_menu[$k]['is_menu'] = 1;
                    }
                }
                foreach ($role_menu_ids as $vrmi) {
                    if ($v['admin_menu_id'] == $vrmi) {
                        $admin_menu[$k]['is_role'] = 1;
                    }
                }
            }

            $admin_menu = MenuService::toTree($admin_menu, 0);

            $data[$pk]              = $admin_user_id;
            $data['admin_menu_ids'] = $admin_menu_ids;
            $data['admin_role_ids'] = $admin_user['admin_role_ids'];
            $data['username']       = $admin_user['username'];
            $data['nickname']       = $admin_user['nickname'];
            $data['menu_ids']       = $menu_ids;
            $data['admin_role']     = $admin_role;
            $data['admin_menu']     = $admin_menu;

            return $data;
        } else {
            $admin_user_id  = $param[$pk];
            $admin_role_ids = $param['admin_role_ids'];
            $admin_menu_ids = $param['admin_menu_ids'];

            sort($admin_role_ids);
            sort($admin_menu_ids);

            $update['admin_role_ids'] = str_join(implode(',', $admin_role_ids));
            $update['admin_menu_ids'] = str_join(implode(',', $admin_menu_ids));
            $update['update_time']    = datetime();

            $res = $model->where($pk, $admin_user_id)->update($update);
            if (empty($res)) {
                exception();
            }

            UserCache::upd($admin_user_id);

            $update[$pk] = $admin_user_id;

            return $update;
        }
    }

    /**
     * 用户重置密码
     *
     * @param array  $ids      用户id
     * @param string $password 新密码
     * 
     * @return array
     */
    public static function pwd($ids, $password)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['password']    = md5($password);
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户是否超管
     *
     * @param array $ids      用户id
     * @param int   $is_super 是否超管
     * 
     * @return array
     */
    public static function super($ids, $is_super)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['is_super']    = $is_super;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户是否禁用
     *
     * @param array $ids        用户id
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户登录
     *
     * @param array $param 登录信息
     * 
     * @return array
     */
    public static function login($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $where[] = ['username|phone|email', '=', $param['username']];
        $where[] = ['password', '=', md5($param['password'])];
        $where[] = ['is_delete', '=', 0];

        $user = $model->field($pk . ',login_num,is_disable')->where($where)->find();
        if (empty($user)) {
            exception('账号或密码错误');
        }
        $user = $user->toArray();
        if ($user['is_disable'] == 1) {
            exception('账号已被禁用，请联系管理员');
        }

        $ip_info = IpInfoUtils::info();

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = datetime();
        $update['login_num']    = $user['login_num'] + 1;
        $model->where($pk, $user[$pk])->update($update);

        $user_log[$pk]             = $user[$pk];
        $user_log['log_type']      = 1;
        $user_log['response_code'] = 200;
        $user_log['response_msg']  = '登录成功';
        UserLogService::add($user_log);

        UserCache::del($user[$pk]);
        $user = self::info($user[$pk]);
        $admin_user_id = $user[$pk];
        $admin_token = $user['admin_token'];

        return compact($pk, 'admin_token');
    }

    /**
     * 用户退出
     *
     * @param int $id 用户id
     * 
     * @return array
     */
    public static function logout($id)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['logout_time'] = datetime();

        $model->where($pk, $id)->update($update);

        UserCache::del($id);

        $update[$pk] = $id;

        return $update;
    }
}
