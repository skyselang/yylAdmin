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
use think\facade\Validate;
use app\common\cache\system\UserCache;
use app\common\model\system\MenuModel;
use app\common\model\system\UserModel;
use app\common\service\system\SettingService;
use app\common\service\system\UserTokenService;
use app\common\service\utils\Utils;
use hg\apidoc\annotation as Apidoc;

/**
 * 用户管理
 */
class UserService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'user_id/d'   => '',
        'avatar_id/d' => 0,
        'number/s'    => '',
        'nickname/s'  => '',
        'username/s'  => '',
        'phone/s'     => '',
        'email/s'     => '',
        'remark/s'    => '',
        'sort/d'      => 250,
    ];

    /**
     * 用户列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * 
     * @return array ['count', 'pages', 'page', 'limit', 'list']
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new UserModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',avatar_id,number,nickname,username,sort,is_super,is_disable,create_time,update_time,login_time';
        } else {
            $field = $group . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $group => 'desc'];
        }

        if (user_hide_where()) {
            $where[] = user_hide_where('a.user_id');
        }

        $model = $model->alias('a');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'dept_ids' && is_array($wv[2])) {
                $model = $model->join('system_user_attributes d', 'a.user_id=d.user_id')->where('d.dept_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
            if ($wv[0] == 'post_ids' && is_array($wv[2])) {
                $model = $model->join('system_user_attributes p', 'a.user_id=p.user_id')->where('p.post_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
            if ($wv[0] == 'role_ids' && is_array($wv[2])) {
                $model = $model->join('system_user_attributes r', 'a.user_id=r.user_id')->where('r.role_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $with     = ['depts', 'posts', 'roles'];
        $append   = ['dept_names', 'post_names', 'role_names'];
        $hidden   = ['depts', 'posts', 'roles'];
        $field_no = [];
        if (strpos($field, 'avatar_id')) {
            $with[]   = $hidden[] = 'avatar';
            $append[] = 'avatar_url';
        }
        if (strpos($field, 'is_super')) {
            $append[] = 'is_super_name';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $pages = 0;
        if ($total) {
            $count_model = clone $model;
            $count = $count_model->where($where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->group($group)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 用户信息
     *
     * @param int  $id   用户id
     * @param bool $exce 不存在是否抛出异常
     * @param bool $role 是否返回角色信息
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true, $role = false)
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
            $info = $info
                ->append(['avatar_url', 'dept_ids', 'post_ids', 'role_ids'])
                ->hidden(['avatar', 'depts', 'posts', 'roles'])
                ->toArray();

            $MenuModel = new MenuModel();
            $MenuPk = $MenuModel->getPk();

            if (user_is_super($id)) {
                $menu      = $MenuModel->field($MenuPk . ',menu_url')->where([where_delete()])->select()->toArray();
                $menu_ids  = array_column($menu, 'menu_id');
                $menu_urls = array_filter(array_column($menu, 'menu_url'));
            } elseif ($info['is_super'] == 1) {
                $menu      = $MenuModel->field($MenuPk . ',menu_url')->where(where_disdel())->select()->toArray();
                $menu_ids  = array_column($menu, 'menu_id');
                $menu_urls = array_filter(array_column($menu, 'menu_url'));
            } else {
                $role_menu_ids   = RoleService::menu_ids($info['role_ids'], where_disdel());
                $unauth_menu_ids = MenuService::unauthList('id');
                $menu_ids        = array_merge($role_menu_ids, $unauth_menu_ids);
                $menu_urls       = $MenuModel->where('menu_id', 'in', $menu_ids)->where(where_disdel())->column('menu_url');
                $menu_urls       = array_filter($menu_urls);
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
            $menu_urls       = array_unique(array_merge($menu_urls, $unlogin_unauth));

            $info['roles']    = array_values($menu_urls);
            $info['menus']    = MenuService::menus($menu_ids);
            $info['menu_ids'] = $menu_ids;

            UserCache::set($id, $info);
        }

        if ($role) {
            $info = self::roleMenu($info);
        }

        return $info;
    }

    /**
     * 用户添加
     *
     * @param array $param 用户信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        // 密码
        if (isset($param['password'])) {
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加部门
            if (isset($param['dept_ids'])) {
                $model->depts()->saveAll($param['dept_ids']);
            }
            // 添加职位
            if (isset($param['post_ids'])) {
                $model->posts()->saveAll($param['post_ids']);
            }
            // 添加角色
            if (isset($param['role_ids'])) {
                $model->roles()->saveAll($param['role_ids']);
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
     * 用户修改
     *
     * @param int|array $ids   用户id
     * @param array     $param 用户信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new UserModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        // 密码
        if (isset($param['password'])) {
            $param['pwd_time'] = datetime();
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['dept_ids', 'post_ids', 'role_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改部门
                    if (isset($param['dept_ids'])) {
                        $info = $info->append(['dept_ids']);
                        relation_update($info, $info['dept_ids'], $param['dept_ids'], 'depts');
                    }
                    // 修改职位
                    if (isset($param['post_ids'])) {
                        $info = $info->append(['post_ids']);
                        relation_update($info, $info['post_ids'], $param['post_ids'], 'posts');
                    }
                    // 修改角色
                    if (isset($param['role_ids'])) {
                        $info = $info->append(['role_ids']);
                        relation_update($info, $info['role_ids'], $param['role_ids'], 'roles');
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

        UserCache::del($ids);

        return $param;
    }

    /**
     * 用户删除
     *
     * @param int|array $ids  用户id
     * @param bool      $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new UserModel();
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
                    // 删除部门
                    $info->depts()->detach();
                    // 删除职位
                    $info->posts()->detach();
                    // 删除角色
                    $info->roles()->detach();
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

        UserCache::del($ids);

        return $update;
    }

    /**
     * 用户角色菜单
     *
     * @param  array $info
     * @return array
     */
    public static function roleMenu($info)
    {
        $user_menu_ids = $info['menu_ids'] ?? [];
        $role_menu_ids = RoleService::menu_ids($info['role_ids'], where_disdel());

        $menu_list = MenuService::list('list', [where_delete()], [], 'menu_id,menu_pid,menu_name,menu_url,is_unlogin,is_unauth');
        foreach ($menu_list as &$val) {
            $val['is_check'] = 0;
            $val['is_role'] = 0;
            foreach ($user_menu_ids as $m_menu_id) {
                if ($val['menu_id'] == $m_menu_id) {
                    $val['is_check'] = 1;
                }
            }
            foreach ($role_menu_ids as $g_menu_id) {
                if ($val['menu_id'] == $g_menu_id) {
                    $val['is_role'] = 1;
                }
            }
        }
        $info['menu_tree'] = list_to_tree($menu_list, 'menu_id', 'menu_pid');

        return $info;
    }

    /**
     * 用户登录
     *
     * @param array $param 登录信息
     * @Apidoc\Returned("AdminToken", type="string", require=true, desc="AdminToken")
     * @return array|Exception
     */
    public static function login($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $field = $pk . ',password,is_disable,login_num';

        if (Validate::rule('username', 'mobile')->check($param)) {
            $where[] = ['phone', '=', $param['username']];
        } else if (Validate::rule('username', 'email')->check($param)) {
            $where[] = ['email', '=', $param['username']];
        } else {
            $where[] = ['username', '=', $param['username']];
        }
        $where[] = where_delete();

        $user = $model->field($field)->where($where)->find();
        if (empty($user)) {
            $user = $model->field($field)->where(where_delete(['username|phone|email', '=', $param['username']]))->find();
        }
        if (empty($user)) {
            exception(lang('system.Account or password error'));
        }

        $user = $user->toArray();
        $password_verify = password_verify($param['password'], $user['password']);
        if (!$password_verify) {
            exception(lang('system.Account or password error'));
        }
        if ($user['is_disable'] == 1) {
            exception(lang('system.The account has been disabled. Please contact the administrator'));
        }

        $user_id = $user[$pk];
        $ip_info = Utils::ipInfo();

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = datetime();
        $update['login_num']    = $user['login_num'] + 1;
        $model->where($pk, $user_id)->update($update);

        $user_log[$pk]             = $user_id;
        $user_log['response_code'] = 200;
        $user_log['response_msg']  = '登录成功';
        UserLogService::add($user_log, SettingService::LOG_TYPE_LOGIN);

        UserCache::del($user_id);
        $user = self::info($user_id);
        $data = self::loginField($user);

        return $data;
    }

    /**
     * 用户登录返回字段
     *
     * @param array $user 用户信息
     *
     * @return array
     */
    public static function loginField($user)
    {
        $data = [];
        $setting = SettingService::info();
        $token_name = $setting['token_name'];
        $data[$token_name] = self::token($user);
        $fields = ['user_id', 'nickname', 'username'];
        foreach ($fields as $field) {
            if (isset($user[$field])) {
                $data[$field] = $user[$field];
            }
        }

        return $data;
    }

    /**
     * 用户token
     *
     * @param  array $user 用户信息
     *
     * @return string
     */
    public static function token($user)
    {
        return UserTokenService::create($user);
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

        $update[$pk] = $id;

        UserCache::del($id);

        return $update;
    }
}
