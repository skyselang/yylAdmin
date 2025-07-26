<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\member\GroupCache as Cache;
use app\common\model\member\GroupModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\ImportService;
use app\common\model\member\MemberModel;
use app\common\cache\member\MemberCache;
use app\common\model\member\AttributesModel;
use think\facade\Db;

/**
 * 会员分组
 */
class GroupService
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
        'group_id'       => '',
        'group_unique/s' => '',
        'group_name/s'   => '',
        'desc/s'         => '',
        'is_default/d'   => 0,
        'api_ids/a'      => [],
        'remark/s'       => '',
        'sort/d'         => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'group_unique', 'is_default', 'api_ids'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned(ref={Model::class,"getApiIdsAttr"}, field="api_ids"),
     *   @Apidoc\Returned("apis", ref={ApiService::class,"info"}, type="tree", desc="接口树形", field="api_id,api_pid,api_name,api_url,is_unlogin,is_unauth,is_disable"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps     = $exp ? where_exps() : [];
        $apis     = ApiService::list('tree', [where_delete()], [], 'api_name,api_url,is_unlogin,is_unauth,is_disable');
        $api_list = ApiService::list('list', [where_delete()], [], 'api_name');
        $api_ids  = array_column($api_list, 'api_id');

        return ['exps' => $exps, 'apis' => $apis, 'api_ids' => $api_ids];
    }

    /**
     * 会员分组列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="group_id,group_unique,group_name,desc,is_default,remark,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDefaultNameAttr"}, field="is_default_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }
        if (empty($field)) {
            $field = $group . ',group_unique,group_name,desc,is_default,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $group . ',' . $field;
        }

        $wt = 'member_group_apis ';
        $wa = 'b';
        $model = $model->alias('a');
        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'api_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.group_id=' . $wa . '.group_id');
                $where[$wk] = [$wa . '.api_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'api_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.group_id=' . $wa . '.group_id');
                $where_scope[] = [$wa . '.api_id', $wv[1], $wv[2]];
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
        if (strpos($field, 'is_default')) {
            $append[] = 'is_default_name';
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
     * 会员分组信息
     * @param int|string $id   分组id
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="group_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getIsDefaultNameAttr"}, field="is_default_name")
     * @Apidoc\Returned(ref={Model::class,"getApiIdsAttr"}, field="api_ids")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['group_unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('会员分组不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_disable_name', 'is_default_name', 'api_ids'])->hidden(['apis'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 会员分组添加
     * @param array $param 分组信息
     * @Apidoc\Param(ref={Model::class}, withoutField="group_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getApiIdsAttr"}, field="api_ids")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['group_unique'] ?? '')) {
            $param['group_unique'] = uniqids();
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加接口
            if (isset($param['api_ids'])) {
                $model->apis()->saveAll($param['api_ids']);
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

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 会员分组修改
     * @param int|array $ids   分组id
     * @param array     $param 分组信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getApiIdsAttr"}, field="api_ids")
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
            if (var_isset($param, ['api_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改接口
                    if (isset($param['api_ids'])) {
                        $info = $info->append(['api_ids']);
                        model_relation_update($info, $info['api_ids'], $param['api_ids'], 'apis');
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
        $cache->clear();

        return $param;
    }

    /**
     * 会员分组删除
     * @param array $ids  分组id
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
                    // 删除接口
                    $info->apis()->detach();
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
        $cache->clear();

        return $update;
    }

    /**
     * 会员分组是否禁用
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
     * 会员分组批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'group_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 会员分组导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $is_default = $exp_imp == 'export' ? 'is_default_name' : 'is_default';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'group_unique', 'name' => lang('编号'), 'width' => 22],
            ['field' => 'group_name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => 'desc', 'name' => lang('描述'), 'width' => 30],
            ['field' => $is_default, 'name' => lang('默认'), 'width' => 10],
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
     * 会员分组导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_MEMBER_GROUP;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 会员分组导入
     * @param array $import_info 导入信息
     * @param bool  $is_add      是否添加导入信息
     * @Apidoc\Query(ref="importParam")
     * @Apidoc\Param(ref="importParam")
     * @Apidoc\Returned(ref="importParam")
     * @Apidoc\Returned(ref={ImportService::class,"info"})
     */
    public static function import($import_info, $is_add = false)
    {
        if ($is_add) {
            $import_info['type'] = FileSettingService::EXPIMP_TYPE_FILE_GROUP;
            $import_id = ImportService::add($import_info);
            $data = ImportService::imports($import_id, __CLASS__, __FUNCTION__);
            return $data;
        }

        $header = self::header('import');
        $import = ImportService::importsReader($header, $import_info['file_path']);
        $model = self::model();
        $table = $model->getTable();
        $pk = $model->getPk();
        $import_num = count($import);
        $success = $fail = [];
        $datetime = datetime();
        $batch_num = 10000;

        while (count($import) > 0) {
            $batchs = array_splice($import, 0, $batch_num);
            foreach ($batchs as $key => $val) {
                $temp = [];
                foreach ($header as $vh) {
                    if ($vh['index'] > -1) {
                        $temp[$vh['field']] = $val[$vh['index']] ?? '';
                    }
                }
                $batchs[$key] = $temp;
            }

            $ids = array_column($batchs, $pk);
            $uniques = array_column($batchs, 'group_unique');
            $ids_repeat = array_repeat($ids);
            $uniques_repeat = array_repeat($uniques);
            $ids = Db::table($table)->where($pk, '>', 0)->where($pk, 'in', $ids)->where('is_delete', 0)->column($pk);
            $uniques = Db::table($table)->where($pk, 'not in', $ids)->where('group_unique', 'in', $uniques)
                ->where('is_delete', 0)->column('group_unique');

            $updates = $inserts = [];
            foreach ($batchs as $batch) {
                $batch['result_msg'] = [];
                if ($batch[$pk]) {
                    if (filter_var($batch[$pk], FILTER_VALIDATE_INT) === false) {
                        $batch['result_msg'][] = lang('ID只能是整数');
                    } elseif (in_array($batch[$pk], $ids_repeat)) {
                        $batch['result_msg'][] = lang('ID重复');
                    } elseif (!$import_info['is_update'] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'][] = lang('ID已存在');
                    }
                }
                if ($batch['group_unique']) {
                    if (is_numeric($batch['group_unique'])) {
                        $batch['result_msg'][] = lang('编号不能为纯数字');
                    } elseif (in_array($batch['group_unique'], $uniques_repeat)) {
                        $batch['result_msg'][] = lang('编号重复');
                    } elseif (in_array($batch['group_unique'], $uniques)) {
                        $batch['result_msg'][] = lang('编号已存在');
                    }
                }
                if (empty($batch['group_name'])) {
                    $batch['result_msg'][] = lang('名称不能为空');
                }
                if ($batch['create_time']) {
                    if (!strtotime($batch['create_time'])) {
                        $batch['result_msg'][] = lang('添加时间格式错误');
                    }
                }
                if ($batch['update_time']) {
                    if (!strtotime($batch['update_time'])) {
                        $batch['result_msg'][] = lang('修改时间格式错误');
                    }
                }

                if ($batch['result_msg']) {
                    $batch['result_msg'] = lang('失败：') . implode('，', $batch['result_msg']);
                    $fail[] = $batch;
                } else {
                    $batch['result_msg'] = lang('成功：');
                    $batch_tmp = $batch;
                    $batch_tmp['is_disable'] = (in_array($batch['is_disable'], ['1', lang('是')])) ? 1 : 0;
                    $batch_tmp['is_default'] = (in_array($batch['is_default'], ['1', lang('是')])) ? 1 : 0;
                    if ($batch[$pk] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'] .= lang('修改');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? null : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? $datetime : $batch['update_time'];
                        $updates[] = $batch_tmp;
                    } else {
                        $batch['result_msg'] .= lang('添加');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? $datetime : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? null : $batch['update_time'];
                        unset($batch_tmp[$pk]);
                        $inserts[] = $batch_tmp;
                    }
                    $success[] = $batch;
                }
            }
            unset($batchs, $uniques);

            if ($updates) {
                batch_update($model, $header, $updates);
            }
            if ($inserts) {
                batch_insert($model, $header, $inserts);
            }
            if ($updates || $inserts) {
                $cache = self::cache();
                $cache->clear();
            }
            unset($updates, $inserts);
        }
        unset($import);

        return ['import_num' => $import_num, 'header' => $header, 'success' => $success, 'fail' => $fail];
    }

    /**
     * 会员分组会员列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref={Model::class}, field="group_id")
     * @Apidoc\Query(ref={MemberService::class,"list"})
     * @Apidoc\Returned(ref={MemberService::class,"list"})
     */
    public static function memberList($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return MemberService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 会员分组会员解除
     * @param array $group_id   分组id
     * @param array $member_ids 会员id
     * @Apidoc\Param("group_id", type="array", require=true, desc="分组id")
     * @Apidoc\Param("member_ids", type="array", require=false, desc="会员id，为空则解除所有会员")
     */
    public static function memberLift($group_id, $member_ids = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        $member_model = new MemberModel();
        $member_pk = $member_model->getPk();

        $where[] = [$pk, 'in', $group_id];
        if (empty($member_ids)) {
            $member_ids = AttributesModel::where($where)->column($member_pk);
        }
        $where[] = [$member_pk, 'in', $member_ids];

        $res = AttributesModel::where($where)->delete();

        $member_cache = new MemberCache();
        $member_cache->del($member_ids);

        return $res;
    }

    /**
     * 会员分组接口id
     * @param int|array $group_ids 分组id
     * @param array     $where     分组条件
     */
    public static function apiIds($group_ids, $where = [])
    {
        if (empty($group_ids)) {
            return [];
        }

        if (is_numeric($group_ids)) {
            $group_ids = [$group_ids];
        }

        $model = self::model();
        $pk    = $model->getPk();

        $where[] = [$pk, 'in', $group_ids];

        $group = $model->field($pk)->where($where)->append(['api_ids'])->select()->toArray();

        $api_ids = [];
        foreach ($group as $v) {
            $api_ids = array_merge($api_ids, $v['api_ids']);
        }

        return $api_ids;
    }

    /**
     * 会员分组默认分组id
     * @return array
     */
    public static function defaultIds()
    {
        $model = self::model();
        $pk    = $model->getPk();
        $ids   = $model->where(where_delete(['is_default', '=', 1]))->column($pk);

        return $ids;
    }

    /**
     * 获取id
     * @param string $name 名称 多个用逗号分隔
     * @return array
     */
    public static function nameId($name)
    {
        $model = self::model();
        $pk    = $model->getPk();
        $namek = $model->namek;

        $key   = 'list-' . $namek;
        $cache = self::cache();
        $list  = $cache->get($key);
        if (empty($list)) {
            $list = self::list([], 0, 0, [], $namek, false)['list'];
            $cache->set($key, $list);
        }

        $ids   = [];
        $names = explode(separator(), $name);
        foreach ($list as $val) {
            if (in_array($val[$namek], $names)) {
                $ids[] = $val[$pk];
            }
        }

        return $ids;
    }
}
