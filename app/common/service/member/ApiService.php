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
use app\common\cache\member\ApiCache as Cache;
use app\common\model\member\ApiModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\ImportService;
use app\common\model\member\GroupModel;
use app\common\cache\member\GroupCache;
use app\common\model\member\GroupApisModel;
use think\facade\Db;

/**
 * 会员接口
 */
class ApiService
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
        'api_id'       => '',
        'api_pid/d'    => 0,
        'api_name/s'   => '',
        'api_url/s'    => '',
        'is_unlogin/b' => 0,
        'is_unauth/b'  => 0,
        'is_unrate/b'  => 0,
        'log_type/d'   => SettingService::LOG_TYPE_OPERATION,
        'remark/s'     => '',
        'sort/d'       => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'api_pid', 'is_unlogin', 'is_unauth', 'is_unrate'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("trees", ref={Model::class}, type="tree", desc="树形", field="api_id,api_pid,api_name"),
     *   @Apidoc\Returned("log_types", type="array", desc="日志类型"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps      = $exp ? where_exps() : [];
        $trees     = ApiService::list('tree', [where_delete()], [], 'api_name');
        $log_types = SettingService::logTypes('', true);

        return ['exps' => $exps, 'trees' => $trees, 'log_types' => $log_types];
    }

    /**
     * 会员接口列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="api_id,api_pid,api_name,api_url,is_unlogin,is_unauth,is_unrate,is_disable,sort"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
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
            $field = $pk . ',' . $pidk . ',api_name,api_url,is_unlogin,is_unauth,is_unrate,is_disable,sort';
        } else {
            $field = $pk . ',' . $pidk . ',' . $field;
        }

        $cache = self::cache();
        $key   = where_cache_key($type, $where, $order, $field, $page, $limit, $param);
        $data  = $cache->get($key);
        if (empty($data)) {
            $append = [];
            if (strpos($field, 'is_disable')) {
                $append[] = 'is_disable_name';
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
     * 会员接口信息
     * @param int|string $id   接口id、url
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="api_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getIsUnloginNameAttr"}, field="is_unlogin_name")
     * @Apidoc\Returned(ref={Model::class,"getIsUnauthNameAttr"}, field="is_unauth_name")
     * @Apidoc\Returned(ref={Model::class,"getIsUnrateNameAttr"}, field="is_unrate_name")
     */
    public static function info($id = '', $exce = true)
    {
        if (empty($id)) {
            $id = api_url();
        }

        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['api_url', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('会员接口不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_disable_name', 'is_unlogin_name', 'is_unauth_name', 'is_unrate_name'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 会员接口添加
     * @param array $param 接口信息
     * @Apidoc\Param(ref={Model::class}, withoutField="api_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
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
     * 会员接口修改
     * @param int|array $ids   接口id
     * @param array     $param 接口信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function edit($ids, $param = [])
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
     * 会员接口删除
     * @param array $ids  接口id
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
     * 会员接口是否禁用
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
     * 会员接口批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'api_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 会员接口修改上级
     * @param array $ids     id
     * @param int   $api_pid 上级ID
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref={Model::class},field="api_pid")
     */
    public static function editPid($ids, $api_pid)
    {
        $data = self::edit($ids, ['api_pid' => $api_pid]);

        return $data;
    }

    /**
     * 会员接口是否免登
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
     * 会员接口是否免权
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
     * 会员接口是否免限
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
     * 会员接口导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $is_unlogin = $exp_imp == 'export' ? 'is_unlogin_name' : 'is_unlogin';
        $is_unauth  = $exp_imp == 'export' ? 'is_unauth_name' : 'is_unauth';
        $is_unrate  = $exp_imp == 'export' ? 'is_unrate_name' : 'is_unrate';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'api_pid', 'name' => lang('上级ID'), 'width' => 12],
            ['field' => 'api_name', 'name' => lang('名称'), 'width' => 26, 'color' => 'FF0000'],
            ['field' => 'api_url', 'name' => lang('链接'), 'width' => 32],
            ['field' => $is_unlogin, 'name' => lang('免登'), 'width' => 10],
            ['field' => $is_unauth, 'name' => lang('免权'), 'width' => 10],
            ['field' => $is_unrate, 'name' => lang('免限'), 'width' => 10],
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
     * 会员接口导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 1;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_MEMBER_API;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 会员接口导入
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
            $import_info['type'] = FileSettingService::EXPIMP_TYPE_MEMBER_API;
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
            $uniques = array_column($batchs, 'api_url');
            $ids_repeat = array_repeat($ids);
            $uniques_repeat = array_repeat($uniques);
            $ids = Db::table($table)->where($pk, '>', 0)->where($pk, 'in', $ids)->where('is_delete', 0)->column($pk);
            $uniques = Db::table($table)->where($pk, 'not in', $ids)->where('api_url', 'in', $uniques)
                ->where('is_delete', 0)->column('api_url');

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
                if ($batch['api_pid']) {
                    if ($batch['api_pid'] == $batch[$pk]) {
                        $batch['result_msg'][] = lang('上级ID不能等于ID');
                    }
                }
                if ($batch['api_url']) {
                    if (is_numeric($batch['api_url'])) {
                        $batch['result_msg'][] = lang('接口链接不能为纯数字');
                    } elseif (in_array($batch['api_url'], $uniques_repeat)) {
                        $batch['result_msg'][] = lang('接口链接重复');
                    } elseif (in_array($batch['api_url'], $uniques)) {
                        $batch['result_msg'][] = lang('接口链接已存在');
                    }
                }
                if (empty($batch['api_name'])) {
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
     * 会员接口分组列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref={Model::class}, field="api_id")
     * @Apidoc\Query(ref={GroupService::class,"list"})
     * @Apidoc\Returned(ref={GroupService::class,"list"})
     */
    public static function groupList($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return GroupService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 会员接口分组解除
     * @param array $api_id    接口id
     * @param array $group_ids 分组id
     * @return int
     * @Apidoc\Param("api_id", type="array", require=true, desc="接口id")
     * @Apidoc\Param("group_ids", type="array", require=false, desc="分组id，为空则解除所有分组")
     */
    public static function groupLift($api_id, $group_ids = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        $group_model = new GroupModel();
        $group_pk = $group_model->getPk();

        $where[] = [$pk, 'in', $api_id];
        if (empty($group_ids)) {
            $group_ids = GroupApisModel::where($where)->column($group_pk);
        }
        $where[] = [$group_pk, 'in', $group_ids];

        $res = GroupApisModel::where($where)->delete();

        $group_unique = $group_model->where($group_pk, 'in', $group_ids)->column('group_unique');
        $group_cache = new GroupCache();
        $group_cache->del($group_ids);
        $group_cache->del($group_unique);

        return $res;
    }

    /**
     * 会员接口列表
     * @param string $type url接口url，id接口id
     * @return array 
     */
    public static function apiList($type = 'url')
    {
        $key = 'api-' . $type;
        $cache = self::cache();
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'api_url';
            if ($type == 'id') {
                $column = $model->getPk();
            }

            $list = $model->where([where_delete()])->column($column);
            $list = array_values(array_filter($list));
            sort($list);

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口免登列表
     * @param string $type url接口url，id接口id
     * @return array
     */
    public static function unloginList($type = 'url')
    {
        $cache = self::cache();
        $key = 'unlogin-' . $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'api_url';
            $api_is_unlogin = config('api.api_is_unlogin', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($api_is_unlogin) {
                    $api_is_unlogin = $model->where('api_url', 'in', $api_is_unlogin)->column($column);
                }
            }

            $list = $model->where(where_delete(['is_unlogin', '=', 1]))->column($column);
            $list = array_merge($list, $api_is_unlogin);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口免权列表
     * @param string $type url接口url，id接口id
     * @return array
     */
    public static function unauthList($type = 'url')
    {
        $cache = self::cache();
        $key = 'unauth-' . $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'api_url';
            $api_is_unauth = config('api.api_is_unauth', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($api_is_unauth) {
                    $api_is_unauth = $model->where('api_url', 'in', $api_is_unauth)->column($column);
                }
            }
            $api_is_unlogin = self::unloginList($type);

            $list = $model->where(where_delete(['is_unauth', '=', 1]))->column($column);
            $list = array_merge($list, $api_is_unlogin, $api_is_unauth);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口免限列表
     * @param string $type url接口url，id接口id
     * @return array
     */
    public static function unrateList($type = 'url')
    {
        $cache = self::cache();
        $key = 'unrate-' . $type;
        $list = $cache->get($key);
        if (empty($list)) {
            $model = self::model();

            $column = 'api_url';
            $api_is_unrate = config('api.api_is_unrate', []);
            if ($type == 'id') {
                $column = $model->getPk();
                if ($api_is_unrate) {
                    $api_is_unrate = $model->where('api_url', 'in', $api_is_unrate)->column($column);
                }
            }

            $list = $model->where(where_delete(['is_unrate', '=', 1]))->column($column);
            $list = array_merge($list, $api_is_unrate);
            $list = array_unique(array_filter($list));
            $list = array_values($list);
            sort($list);

            $cache->set($key, $list);
        }

        return $list;
    }

    /**
     * 会员接口重置id
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
