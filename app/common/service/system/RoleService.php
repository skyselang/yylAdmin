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
use app\common\cache\system\RoleCache as Cache;
use app\common\model\system\RoleModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\cache\system\UserCache;
use app\common\model\system\UserAttributesModel;

/**
 * 角色管理
 */
class RoleService
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
        'role_id'       => '',
        'role_unique/s' => '',
        'role_name/s'   => '',
        'desc/s'        => '',
        'remark/s'      => '',
        'sort/d'        => 250,
        'menu_ids/a'    => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['role_unique', 'remark', 'sort', 'menu_ids'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("menus", ref={MenuService::class}, type="tree", desc="菜单树形", field="menu_id,menu_pid,menu_name,menu_url,is_unlogin,is_unauth,is_unrate,is_disable"),
     *   @Apidoc\Returned(ref={Model::class,"getMenuIdsAttr"}, field="menu_ids"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps      = $exp ? where_exps() : [];
        $menus     = MenuService::list('tree', [where_delete()], [], 'menu_name,menu_url,is_unlogin,is_unauth,is_unrate,is_disable');
        $menu_list = MenuService::list('list', [where_delete()], [], 'menu_name');
        $menu_ids  = array_column($menu_list, 'menu_id');


        return ['exps' => $exps, 'menus' => $menus, 'menu_ids' => $menu_ids];
    }

    /**
     * 角色列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="role_id,role_unique,role_name,desc,remark,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }
        if (empty($field)) {
            $field = 'a.' . $pk . ',role_unique,role_name,desc,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = 'a.' . $pk . ',' . $field;
        }

        $wt = 'system_role_menus ';
        $wa = 'b';
        $model = $model->alias('a');
        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'menu_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.role_id=' . $wa . '.role_id', 'left');
                $where[$wk] = [$wa . '.menu_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'menu_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.role_id=' . $wa . '.role_id', 'left');
                $where_scope[] = [$wa . '.menu_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === $pk) {
                $where[$wk] = ['a.' . $wv[0], $wv[1], $wv[2]];
            }
        }
        $where = array_values($where);

        $append = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }

        $count = $pages = 0;
        if ($total) {
            $count = model_where(clone $model, $where, $where_scope)->count();
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
        $list  = $model->append($append)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 角色信息
     * @param int  $id   角色id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="role_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getMenuIdsAttr"}, field="menu_ids")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('角色不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['menu_ids', 'is_disable_name'])->hidden(['menus'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 角色添加
     * @param array $param 角色信息
     * @Apidoc\Param(ref={Model::class}, withoutField="role_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getMenuIdsAttr"}, field="menu_ids")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['role_unique'] ?? '')) {
            $param['role_unique'] = uniqids();
        }
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
     * @param int|array $ids   角色id
     * @param array     $param 角色信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getMenuIdsAttr"}, field="menu_ids")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

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
                        model_relation_update($info, $info['menu_ids'], $param['menu_ids'], 'menus');
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
     * 角色删除
     * @param array $ids  角色id
     * @param bool  $real 是否真实删除
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
                    // 删除菜单
                    $info->menus()->detach();
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
     * 角色是否禁用
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
     * 角色批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'role_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 角色导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'role_unique', 'name' => lang('编号'), 'width' => 22],
            ['field' => 'role_name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => 'desc', 'name' => lang('描述'), 'width' => 30],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
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
     * 角色导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SYSTEM_ROLE;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 角色用户列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref={Model::class}, field="role_id")
     * @Apidoc\Query(ref={UserService::class,"list"})
     * @Apidoc\Returned(ref={UserService::class,"list"})
     */
    public static function userList($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return UserService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 角色用户解除
     * @param array $role_id  角色id
     * @param array $user_ids 用户id
     * @Apidoc\Param("role_id", type="array", require=true, desc="角色id")
     * @Apidoc\Param("user_ids", type="array", require=false, desc="用户id，为空则解除所有用户")
     */
    public static function userLift($role_id, $user_ids = [])
    {
        $where[] = ['role_id', 'in', $role_id];
        if (empty($user_ids)) {
            $user_ids = UserAttributesModel::where($where)->column('user_id');
        }
        $where[] = ['user_id', 'in', $user_ids];

        $res = UserAttributesModel::where($where)->delete();

        $cache = new UserCache();
        $cache->del($user_ids);

        return $res;
    }

    /**
     * 角色菜单id
     * @param int|array $role_id 角色id
     * @param array     $where   查询条件
     */
    public static function menu_ids($role_id, $where = [])
    {
        if (empty($role_id)) {
            return [];
        }

        if (is_numeric($role_id)) {
            $role_id = [$role_id];
        }

        $RoleModel = self::model();
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
