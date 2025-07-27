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
use app\common\cache\system\MenuCache as Cache;
use app\common\model\system\MenuModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\cache\system\RoleCache;
use app\common\cache\system\UserCache;
use app\common\model\system\RoleMenusModel;
use think\facade\Db;

/**
 * 菜单管理
 */
class MenuService
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
        'menu_id'          => '',
        'menu_pid/d'       => 0,
        'menu_type/d'      => SettingService::MENU_TYPE_CATALOGUE,
        'meta_icon/s'      => '',
        'menu_name/s'      => '',
        'menu_url/s'       => '',
        'path/s'           => '',
        'component/s'      => '',
        'name/s'           => '',
        'meta_query/s'     => '',
        'hidden/d'         => 0,
        'keep_alive/d'     => 1,
        'always_show/d'    => 0,
        'active_menu_id/d' => 0,
        'log_type/d'       => SettingService::LOG_TYPE_OPERATION,
        'remark/s'         => '',
        'sort/d'           => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'hidden', 'active_menu_id'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("trees", ref={Model::class}, type="tree", desc="树形", field="menu_id,menu_pid,menu_name"),
     *   @Apidoc\Returned("menu_types", type="array", desc="菜单类型"),
     *   @Apidoc\Returned("log_types", type="array", desc="日志类型"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps       = $exp ? where_exps('', true) : [];
        $trees      = self::list('tree', [where_delete()], [], 'menu_name');
        $menu_types = SettingService::menuTypes('', true);
        $log_types  = SettingService::logTypes('', true);

        return ['exps' => $exps, 'trees' => $trees, 'menu_types' => $menu_types, 'log_types' => $log_types];
    }

    /**
     * 菜单列表
     * @param string $type  tree树形，list列表
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * @param int    $page  页数
     * @param int    $limit 数量
     * @return array []
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned("list", type="tree", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="menu_id,menu_pid,menu_name,menu_type,meta_icon,menu_url,path,name,component,hidden,sort,is_unlogin,is_unauth,is_unrate,is_disable"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getMenuTypeNameAttr"}, field="menu_type_name"),
     *   @Apidoc\Returned(ref={Model::class,"getHiddenNameAttr"}, field="hidden_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsUnloginNameAttr"}, field="is_unlogin_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsUnauthNameAttr"}, field="is_unauth_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsUnrateNameAttr"}, field="is_unrate_name"),
     * })
     */
    public static function list($type = 'tree', $where = [], $order = [], $field = '', $page = 0, $limit = 0, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'asc'];
        }
        if (empty($field)) {
            $field = $pk . ',' . $pidk . ',menu_name,menu_type,meta_icon,menu_url,path,name,component,hidden,is_unlogin,is_unauth,is_unrate,sort,is_disable';
        } else {
            $field = $pk . ',' . $pidk . ',' . $field;
        }

        $cache = self::cache();
        $key   = where_cache_key($type, $where, $order, $field, $page, $limit, $param);
        $data  = $cache->get($key);
        if (empty($data)) {
            $append = [];
            if (strpos($field, 'menu_type')) {
                $append[] = 'menu_type_name';
            }
            if (strpos($field, 'hidden')) {
                $append[] = 'hidden_name';
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
            if ($page > 0) {
                $model = $model->page($page);
            }
            if ($limit > 0) {
                $model = $model->limit($limit);
            }
            $model = $model->field($field);
            $model = model_where($model, $where);
            $data  = $model->append($append)->order($order)->select()->toArray();
            if ($type === 'tree') {
                $data = array_to_tree($data, $pk, $pidk);
            }
            $cache->set($key, $data);
        }

        return $data;
    }

    /**
     * 菜单信息
     * @param int|string $id   菜单id、url
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="menu_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getMenuTypeNameAttr"}, field="menu_type_name")
     * @Apidoc\Returned(ref={Model::class,"getHiddenNameAttr"}, field="hidden_name")
     * @Apidoc\Returned(ref={Model::class,"getIsUnloginNameAttr"}, field="is_unlogin_name")
     * @Apidoc\Returned(ref={Model::class,"getIsUnauthNameAttr"}, field="is_unauth_name")
     * @Apidoc\Returned(ref={Model::class,"getIsUnrateNameAttr"}, field="is_unrate_name")
     */
    public static function info($id = '', $exce = true)
    {
        if (empty($id)) {
            $id = menu_url();
        }

        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
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
                    exception(lang('菜单不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['menu_type_name', 'hidden_name', 'is_unlogin_name', 'is_unauth_name', 'is_unrate_name', 'is_disable_name'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 菜单添加
     * @param array $param 菜单信息
     * @Apidoc\Param(ref={Model::class}, withoutField="menu_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 菜单修改
     * @param int|array $ids   菜单id
     * @param array     $param 菜单信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function edit($ids, $param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 菜单删除
     * @param array $ids  菜单id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = update_softdele();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->clear();

        return $update;
    }

    /**
     * 菜单是否禁用
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
     * 菜单批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'menu_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 菜单修改上级
     * @param array $ids      id
     * @param int   $menu_pid 上级ID
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref={Model::class},field="menu_pid")
     */
    public static function editPid($ids, $menu_pid)
    {
        $data = self::edit($ids, ['menu_pid' => $menu_pid]);

        return $data;
    }

    /**
     * 菜单修改免登
     * @param array $ids        id
     * @param int   $is_unlogin 是否免登
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref={Model::class},field="is_unlogin")
     */
    public static function editUnlogin($ids, $is_unlogin)
    {
        $data = self::edit($ids, ['is_unlogin' => $is_unlogin]);

        return $data;
    }

    /**
     * 菜单修改免权
     * @param array $ids       id
     * @param int   $is_unauth 是否免登
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref={Model::class},field="is_unauth")
     */
    public static function editUnauth($ids, $is_unauth)
    {
        $data = self::edit($ids, ['is_unauth' => $is_unauth]);

        return $data;
    }

    /**
     * 菜单修改免限
     * @param array $ids       id
     * @param int   $is_unrate 是否免限
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref={Model::class},field="is_unrate")
     */
    public static function editUnrate($ids, $is_unrate)
    {
        $data = self::edit($ids, ['is_unrate' => $is_unrate]);

        return $data;
    }

    /**
     * 菜单导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $menu_type  = $exp_imp == 'export' ? 'menu_type_name' : 'menu_type';
        $hidden     = $exp_imp == 'export' ? 'hidden_name' : 'hidden';
        $is_unlogin = $exp_imp == 'export' ? 'is_unlogin_name' : 'is_unlogin';
        $is_unauth  = $exp_imp == 'export' ? 'is_unauth_name' : 'is_unauth';
        $is_unrate  = $exp_imp == 'export' ? 'is_unrate_name' : 'is_unrate';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'menu_pid', 'name' => lang('上级ID'), 'width' => 12],
            ['field' => $menu_type, 'name' => lang('类型'), 'width' => 10],
            ['field' => 'meta_icon', 'name' => lang('图标'), 'width' => 12],
            ['field' => 'menu_name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => 'menu_url', 'name' => lang('链接'), 'width' => 30],
            ['field' => 'path', 'name' => lang('路由地址'), 'width' => 16],
            ['field' => 'name', 'name' => lang('路由名称'), 'width' => 16],
            ['field' => 'component', 'name' => lang('组件地址'), 'width' => 20],
            ['field' => $hidden, 'name' => lang('隐藏'), 'width' => 10],
            ['field' => $is_unlogin, 'name' => lang('免登'), 'width' => 10],
            ['field' => $is_unauth, 'name' => lang('免权'), 'width' => 10],
            ['field' => $is_unrate, 'name' => lang('免限'), 'width' => 10],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22, 'type' => 'time'],
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
     * 菜单导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 1;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SYSTEM_MENU;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 菜单角色列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref={Model::class}, field="menu_id")
     * @Apidoc\Query(ref={RoleService::class,"list"})
     * @Apidoc\Returned(ref={RoleService::class,"list"})
     */
    public static function roleList($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return RoleService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 菜单角色解除
     * @param array $menu_id  菜单id
     * @param array $role_ids 角色id
     * @Apidoc\Param("menu_id", type="array", require=true, desc="菜单id")
     * @Apidoc\Param("role_ids", type="array", require=false, desc="角色id，为空则解除所有菜单")
     */
    public static function roleLift($menu_id, $role_ids = [])
    {
        $where[] = ['menu_id', 'in', $menu_id];
        if (empty($role_ids)) {
            $role_ids = RoleMenusModel::where($where)->column('role_id');
        }
        $where[] = ['role_id', 'in', $role_ids];

        $res = RoleMenusModel::where($where)->delete();

        $cache = new RoleCache();
        $cache->del($role_ids);
        $cache = new UserCache();
        $cache->clear();

        return $res;
    }

    /**
     * 菜单列表
     * @param string $type url菜单url，id菜单id
     */
    public static function menuList($type = 'url')
    {
        $cache = self::cache();
        $key = $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'menu_url';
            if ($type == 'id') {
                $column = $model->getPk();
            }

            $list = $model->where([where_delete()])->column($column);
            $list = array_filter($list);
            $list = array_values($list);
            sort($list);

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免登列表
     * @param string $type url菜单url，id菜单id
     */
    public static function unloginList($type = 'url')
    {
        $cache = self::cache();
        $key = 'unlogin-' . $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'menu_url';
            $menu_is_unlogin = config('admin.menu_is_unlogin', []);
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

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免权列表
     * @param string $type url菜单url，id菜单id
     */
    public static function unauthList($type = 'url')
    {
        $cache = self::cache();
        $key = 'unauth-' . $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'menu_url';
            $menu_is_unauth = config('admin.menu_is_unauth', []);
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

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单免限列表
     * @param string $type url菜单url，id菜单id
     */
    public static function unrateList($type = 'url')
    {
        $cache = self::cache();
        $key = 'unrate-' . $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'menu_url';
            $menu_is_unrate = config('admin.menu_is_unrate', []);
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

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单路由
     * @param array $ids 菜单id
     */
    public static function menus($ids = [])
    {
        $where = where_delete(['menu_id', 'in', $ids]);
        $field = 'menu_name,menu_type,path,name,component,meta_icon,meta_query,hidden,keep_alive,always_show,active_menu_id';
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
                $tmp['meta']['alwaysShow'] = false;
                $tmp['meta']['activeMenu'] = self::paths($menu, $v['active_menu_id']);
                $tmp['meta']['activeMenuTop'] = self::paths($menu, $v['menu_id'], 1);
                if ($v['menu_type'] == SettingService::MENU_TYPE_CATALOGUE) {
                    $tmp['redirect']  = 'noRedirect';
                    $tmp['component'] = 'Layout';
                    if ($v['menu_pid'] > 0) {
                        $tmp['redirect']  = $v['component'];
                        $tmp['component'] = $v['component'];
                    }
                    $tmp['meta']['alwaysShow'] = $v['always_show'] ? true : false;
                } elseif ($v['menu_type'] == SettingService::MENU_TYPE_MENU) {
                    $tmp['meta']['query'] = $v['meta_query'] ? json_decode($v['meta_query'], true) : [];
                    $tmp['component'] = $v['component'];
                    if (self::isExternal($v['path'])) {
                        unset($tmp['name']);
                    }
                }
                $list[] = $tmp;
            }
        }

        return list_to_tree($list, 'menu_id', 'menu_pid');
    }

    /**
     * 菜单路径（路由地址）
     * @param array $menu    菜单列表
     * @param int   $menu_id 菜单id
     * @param int   $level   层级
     */
    public static function paths($menu, $menu_id, $level = 0)
    {
        $paths = [];
        if ($menu_id) {
            $parents = children_parent_key($menu, $menu_id, 'menu_id', 'menu_pid', 'path');
            $parents = array_reverse($parents);
            foreach ($parents as $lv => $parent) {
                $paths[] = $parent;
                if ($level && ($lv + 1) == $level) {
                    break;
                }
            }
        }
        $paths = rtrim(implode('/', $paths), '/');

        return $paths;
    }

    /**
     * 菜单是否外部链接
     * @param string $path 路由地址
     */
    public static function isExternal($path)
    {
        $protocols = ['http:', 'https:', 'mailto:', 'tel:'];
        foreach ($protocols as $protocol) {
            if (strpos($path, $protocol) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 菜单重置id
     * @return array
     */
    public static function resetId()
    {
        // 判断是否为调试模式且当前用户是否为超管
        if (!config('app.app_debug') || !user_is_super(user_id(true))) {
            exception(lang('不允许此操作'));
        }

        $db    = Db::class;
        $model = self::model();
        $table = $model->getTable();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;

        // 获取所有数据（按层级排序）
        $list = $db::table($table)
            ->where([where_delete()])
            ->order(['sort' => 'desc', $pk => 'asc'])
            ->select()
            ->toArray();

        if (empty($list)) {
            return ['total' => 0, 'id_mapping' => []];
        }

        // 构建树形结构
        $tree = array_to_tree($list, $pk, $pidk);

        // 生成新的ID映射
        $id_mapping = [];
        $new_id     = 1;

        // 递归处理树形结构，按层级顺序分配新ID
        $process_tree = function ($nodes) use (&$process_tree, &$id_mapping, &$new_id, $pk) {
            foreach ($nodes as $node) {
                $old_id = $node[$pk];
                $id_mapping[$old_id] = $new_id;
                $new_id++;

                // 处理子节点
                if (!empty($node['children'])) {
                    $process_tree($node['children'], $id_mapping[$old_id]);
                }
            }
        };

        $process_tree($tree);

        // 开始事务
        $db::startTrans();
        try {
            // 更新所有数据的ID和父级ID
            foreach ($list as $val) {
                $old_id  = $val[$pk];
                $new_id  = $id_mapping[$old_id];
                $new_pid = $val[$pidk] > 0 ? $id_mapping[$val[$pidk]] : 0;

                // 先更新父级ID
                $db::table($table)->where($pk, $old_id)->update([$pidk => $new_pid]);
            }

            // 然后更新主键ID（需要临时表来避免主键冲突）
            $temp_table = $table . '_reset_temp';

            // 创建临时表
            $create_temp_sql = "CREATE TABLE {$temp_table} LIKE {$model->getTable()}";
            $db::execute($create_temp_sql);

            // 复制数据到临时表，使用新ID
            foreach ($list as $val) {
                $old_id     = $val[$pk];
                $new_id     = $id_mapping[$old_id];
                $val[$pk]   = $new_id;
                $val[$pidk] = $val[$pidk] > 0 ? $id_mapping[$val[$pidk]] : 0;

                // 插入到临时表
                $db::table($temp_table)->insert($val);
            }

            // 删除原表数据
            $db::table($table)->where($pk, '>', 0)->delete();

            // 从临时表复制数据回原表
            $copy_sql = "INSERT INTO {$model->getTable()} SELECT * FROM {$temp_table}";
            $db::execute($copy_sql);

            // 删除临时表
            $drop_sql = "DROP TABLE {$temp_table}";
            $db::execute($drop_sql);

            // 重置表自增ID
            $max_id = max(array_values($id_mapping));
            $reset_auto_increment_sql = "ALTER TABLE {$model->getTable()} AUTO_INCREMENT = " . ($max_id + 1);
            $db::execute($reset_auto_increment_sql);

            // 提交事务
            $db::commit();

            // 清除缓存
            $cache = self::cache();
            $cache->clear();

            return ['total' => count($list), 'id_mapping' => $id_mapping, 'auto_increment' => $max_id];
        } catch (\Exception $e) {
            // 回滚事务
            $db::rollback();

            // 删除可能存在的临时表
            try {
                $drop_sql = "DROP TABLE IF EXISTS {$temp_table}";
                $db::execute($drop_sql);
            } catch (\Exception $e) {
                // 忽略删除临时表的错误
            }

            // 抛出异常
            exception(lang('ID重置失败：') . $e->getMessage());
        }
    }
}
