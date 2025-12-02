<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\system\UserCache as Cache;
use app\common\model\system\UserModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\model\system\MenuModel;
use app\common\utils\Utils;

/**
 * 用户管理
 */
class UserService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 添加修改字段
     */
    public static $editField = [
        'user_id'     => '',
        'unique/s'    => '',
        'avatar_id/d' => 0,
        'unique/s'    => '',
        'nickname/s'  => '',
        'username/s'  => '',
        'phone/s'     => '',
        'email/s'     => '',
        'gender/d'    => 0,
        'birthday'    => NULL,
        'remark/s'    => '',
        'sort/d'      => 250,
        'dept_ids/a'  => [],
        'post_ids/a'  => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique', 'avatar_id', 'dept_ids', 'post_ids'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("depts", ref={DeptService::class,"info"}, type="tree", desc="部门树形", field="dept_id,dept_pid,dept_name"),
     *   @Apidoc\Returned("posts", ref={PostService::class,"info"}, type="tree", desc="职位树形", field="post_id,post_pid,post_name"),
     *   @Apidoc\Returned("roles", ref={RoleService::class,"info"}, type="array", desc="角色列表", field="role_id,role_name"),
     *   @Apidoc\Returned("genders", ref={SettingService::class,"genders"}, type="array", desc="性别列表")
     * })
     */
    public static function basedata($exp = false)
    {
        $exps     = $exp ? where_exps() : [];
        $depts    = DeptService::list('tree', [where_delete()], [], 'dept_name');
        $posts    = PostService::list('tree', [where_delete()], [], 'post_name');
        $roles    = RoleService::list([where_delete()], 0, 0, [], 'role_name', false)['list'] ?? [];
        $menus    = MenuService::list('list', [where_delete()], [], 'menu_name');
        $menu_ids = array_column($menus, 'menu_id');
        $genders  = SettingService::genders('', true);

        return [
            'exps'     => $exps,
            'depts'    => $depts,
            'posts'    => $posts,
            'roles'    => $roles,
            'menu_ids' => $menu_ids,
            'genders'  => $genders,
        ];
    }

    /**
     * 用户列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @param array  $param 参数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref={Model::class,"getDeptIdsAttr"}, field="dept_ids")
     * @Apidoc\Query(ref={Model::class,"getPostIdsAttr"}, field="post_ids")
     * @Apidoc\Query(ref={Model::class,"getRoleIdsAttr"}, field="role_ids")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="user_id,unique,avatar_id,nickname,username,sort,is_super,is_disable,login_time,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getAvatarUrlAttr"}, field="avatar_url"),
     *   @Apidoc\Returned(ref={Model::class,"getIsSuperNameAttr"}, field="is_super_name"),
     *   @Apidoc\Returned(ref={Model::class,"getDeptNamesAttr"}, field="dept_names"),
     *   @Apidoc\Returned(ref={Model::class,"getPostNamesAttr"}, field="post_names"),
     *   @Apidoc\Returned(ref={Model::class,"getRoleNamesAttr"}, field="role_names"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $group => 'desc'];
        }
        if (empty($field)) {
            $field = $group . ',unique,avatar_id,nickname,username,sort,is_super,is_disable,login_time,create_time,update_time';
        } else {
            $field = $group . ',' . $field;
        }

        if ($param['dept_ids'] ?? []) {
            $where[] = ['dept_ids', 'in', $param['dept_ids']];
        }
        if ($param['post_ids'] ?? []) {
            $where[] = ['post_ids', 'in', $param['post_ids']];
        }
        if ($param['role_ids'] ?? []) {
            $where[] = ['role_ids', 'in', $param['role_ids']];
        }

        $wt = 'system_user_attributes ';
        $wa = 'b';
        $model = $model->alias('a');
        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'dept_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.user_id=' . $wa . '.user_id');
                $where[$wk] = [$wa . '.dept_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'dept_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.user_id=' . $wa . '.user_id');
                $where_scope[] = [$wa . '.dept_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === 'post_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.user_id=' . $wa . '.user_id');
                $where[$wk] = [$wa . '.post_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'post_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.user_id=' . $wa . '.user_id');
                $where_scope[] = [$wa . '.post_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === 'role_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.user_id=' . $wa . '.user_id');
                $where[$wk] = [$wa . '.role_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'role_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.user_id=' . $wa . '.user_id');
                $where_scope[] = [$wa . '.role_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === $pk) {
                $where[$wk] = ['a.' . $wv[0], $wv[1], $wv[2]];
            }
        }
        $where = array_values($where);
        if (user_hide_where()) {
            $where_scope[] = user_hide_where('a.user_id');
        }

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
        $field = select_field($field, $field_no);

        $count = $pages = 0;
        if ($total) {
            $count = model_where(clone $model, $where, $where_scope)->group($group)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where, $where_scope);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->group($group)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 用户信息
     * @param int  $id   用户id
     * @param bool $exce 不存在是否抛出异常
     * @param bool $role 是否返回角色信息
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="user_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getAvatarUrlAttr"}, field="avatar_url")
     * @Apidoc\Returned(ref={Model::class,"getGenderNameAttr"}, field="gender_name")
     * @Apidoc\Returned(ref={Model::class,"getAgeAttr"}, field="age")
     * @Apidoc\Returned(ref={Model::class,"getIsSuperNameAttr"}, field="is_super_name")
     * @Apidoc\Returned(ref={Model::class,"getDeptNamesAttr"}, field="dept_names")
     * @Apidoc\Returned(ref={Model::class,"getPostNamesAttr"}, field="post_names")
     * @Apidoc\Returned(ref={Model::class,"getRoleNamesAttr"}, field="role_names")
     * @Apidoc\Returned("menus", type="array", desc="菜单路由")
     * @Apidoc\Returned("roles", type="array", desc="菜单链接（权限标识）")
     */
    public static function info($id, $exce = true, $role = false)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('用户不存在：') . $id);
                }
                return [];
            }
            $info = $info
                ->append(['avatar_url', 'gender_name', 'age', 'dept_ids', 'post_ids', 'role_ids', 'is_super_name', 'is_disable_name', 'dept_names', 'post_names', 'role_names'])
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

            $menu_is_unlogin = config('admin.menu_is_unlogin', []);
            $menu_is_unauth  = config('admin.menu_is_unauth', []);
            $unlogin_unauth  = array_merge($menu_is_unlogin, $menu_is_unauth);
            $menu_urls       = array_unique(array_merge($menu_urls, $unlogin_unauth));
            sort($menu_urls);

            $info['roles']    = array_values($menu_urls);
            $info['menus']    = MenuService::menus($menu_ids);
            $info['menu_ids'] = $menu_ids;

            $cache->set($id, $info);
        }

        if ($role) {
            $info['role_menu'] = self::roleMenu($info);
        }

        return $info;
    }

    /**
     * 用户添加
     * @param array $param 用户信息
     * @Apidoc\Param(ref={Model::class}, withoutField="user_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getDeptIdsAttr"}, field="dept_ids")
     * @Apidoc\Param(ref={Model::class,"getPostIdsAttr"}, field="post_ids")
     * @Apidoc\Param(ref={Model::class,"getRoleIdsAttr"}, field="role_ids")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
        }
        if (isset($param['password'])) {
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

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
     * @param int|array $ids   用户id
     * @param array     $param 用户信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getDeptIdsAttr"}, field="dept_ids")
     * @Apidoc\Param(ref={Model::class,"getPostIdsAttr"}, field="post_ids")
     * @Apidoc\Param(ref={Model::class,"getRoleIdsAttr"}, field="role_ids")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        if (isset($param['password'])) {
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
            $param['pwd_time'] = datetime();
        }
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
            if (var_isset($param, ['dept_ids', 'post_ids', 'role_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改部门
                    if (isset($param['dept_ids'])) {
                        $info = $info->append(['dept_ids']);
                        model_relation_update($info, $info['dept_ids'], $param['dept_ids'], 'depts');
                    }
                    // 修改职位
                    if (isset($param['post_ids'])) {
                        $info = $info->append(['post_ids']);
                        model_relation_update($info, $info['post_ids'], $param['post_ids'], 'posts');
                    }
                    // 修改角色
                    if (isset($param['role_ids'])) {
                        $info = $info->append(['role_ids']);
                        model_relation_update($info, $info['role_ids'], $param['role_ids'], 'roles');
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

        $cache = self::cache();
        $cache->del($ids);

        return $param;
    }

    /**
     * 用户删除
     * @param int|array $ids  用户id
     * @param bool      $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

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
                $update = update_softdele();
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

        $cache = self::cache();
        $cache->del($ids);

        return $update;
    }

    /**
     * 用户是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        return $data;
    }

    /**
     * 用户批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 用户修改密码
     * @param array  $ids      id
     * @param string $password 密码
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("password", type="string", require=true, desc="密码")
     */
    public static function editPwd($ids, $password)
    {
        $data = self::edit($ids, ['password' => $password]);

        return $data;
    }

    /**
     * 用户修改角色
     * @param array $ids      id
     * @param array $role_ids 角色id
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("role_ids", type="array", require=true, desc="角色id")
     */
    public static function editRole($ids, $role_ids)
    {
        $data = self::edit($ids, ['role_ids' => $role_ids]);

        return $data;
    }

    /**
     * 用户修改超管
     * @param array $ids      id
     * @param int   $is_super 是否超管
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("is_super", type="int", require=true, desc="是否超管")
     */
    public static function editSuper($ids, $is_super)
    {
        $data = self::edit($ids, ['is_super' => $is_super]);

        return $data;
    }

    /**
     * 用户导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $avatar_id  = $exp_imp == 'export' ? 'avatar_url' : 'avatar_id';
        $is_super   = $exp_imp == 'export' ? 'is_super_name' : 'is_super';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 10],
            ['field' => 'unique', 'name' => lang('编号'), 'width' => 20],
            ['field' => $avatar_id, 'name' => lang('头像'), 'width' => 20],
            ['field' => 'nickname', 'name' => lang('昵称'), 'width' => 20, 'color' => 'FF0000'],
            ['field' => 'username', 'name' => lang('账号'), 'width' => 20, 'color' => 'FF0000'],
            ['field' => 'phone', 'name' => lang('手机'), 'width' => 16],
            ['field' => 'email', 'name' => lang('邮箱'), 'width' => 30],
            ['field' => $is_super, 'name' => lang('超管'), 'width' => 10],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22],
        ];
        // 生成下标
        foreach ($header as $index => &$value) {
            $value['index'] = $index;
        }
        if ($exp_imp == 'import') {
            $header[] = ['index' => -1, 'field' => 'result_msg', 'name' => lang('导入结果'), 'width' => 60];
        }

        return $header;
    }

    /**
     * 用户导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SYSTEM_USER;

        $field = '';
        $limit = 2500;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 用户角色菜单
     * @param array $info 用户信息
     */
    public static function roleMenu($info)
    {
        $user_menu_ids = $info['menu_ids'] ?? [];
        $role_menu_ids = RoleService::menu_ids($info['role_ids'], where_disdel());

        $menu_list = MenuService::list('list', [where_delete()], [], 'menu_name,menu_url,is_unlogin,is_unauth');
        foreach ($menu_list as &$val) {
            $val['is_check'] = 0;
            $val['is_role']  = 0;
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
        $role_menu = list_to_tree($menu_list, 'menu_id', 'menu_pid');

        return $role_menu;
    }

    /**
     * 用户登录
     * @param array $param 登录信息
     * @Apidoc\Param(ref={Model::class}, field="username,password")
     * @Apidoc\Returned(ref={Model::class}, field="user_id,nickname,username")
     * @Apidoc\Returned("AdminToken", type="string", require=true, desc="AdminToken")
     */
    public static function login($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $field = $pk . ',password,is_disable,login_num,login_ip,login_region,login_time';

        if (validate(['username' => 'mobile'], [], false, false)->check($param)) {
            $where[] = ['phone', '=', $param['username']];
        } else if (validate(['username' => 'email'], [], false, false)->check($param)) {
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
            exception(lang('账号或密码错误'));
        }

        $user = $user->toArray();
        $user_id = $user[$pk];
        
        $password_verify = password_verify($param['password'], $user['password']);
        if (!$password_verify) {
            // 设置 user_id 到应用容器，以便中间件记录登录失败日志
            app()->instance('login_fail_user_id', $user_id);
            exception(lang('账号或密码错误'));
        }
        if ($user['is_disable'] == 1) {
            // 设置 user_id 到应用容器，以便中间件记录登录失败日志
            app()->instance('login_fail_user_id', $user_id);
            exception(lang('账号已被禁用'));
        }

        $ip_info = Utils::ipInfo();

        $update['login_num']    = $user['login_num'] + 1;
        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = datetime();
        // 上次登录信息
        $update['last_login_ip']     = $user['login_ip'];
        $update['last_login_region'] = $user['login_region'];
        $update['last_login_time']   = $user['login_time'];
        $model->where($pk, $user_id)->update($update);

        $cache = self::cache();
        $cache->del($user_id);
        $user = self::info($user_id);
        $data = self::loginField($user);

        return $data;
    }

    /**
     * 用户登录返回字段
     * @param array $user 用户信息
     */
    public static function loginField($user)
    {
        $data = [];
        $fields = ['user_id', 'nickname', 'username'];
        foreach ($fields as $field) {
            if (isset($user[$field])) {
                $data[$field] = $user[$field];
            }
        }

        $setting = SettingService::info();
        $token_name = $setting['token_name'];
        $data[$token_name] = self::token($user);

        return $data;
    }

    /**
     * 用户token
     * @param array $user 用户信息
     */
    public static function token($user)
    {
        return UserTokenService::create($user);
    }

    /**
     * 用户退出
     * @param int $id 用户id
     */
    public static function logout($id)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $update['logout_time'] = datetime();

        $model->where($pk, $id)->update($update);

        $update[$pk] = $id;

        $cache = self::cache();
        $cache->del($id);

        return $update;
    }
}
