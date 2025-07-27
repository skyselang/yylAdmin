<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\setting\RegionCache as Cache;
use app\common\model\setting\RegionModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use Overtrue\Pinyin\Pinyin;

/**
 * 地区设置
 */
class RegionService
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
        'region_id'     => '',
        'region_pid/d'  => 0,
        'region_name/s' => '',
        'level/d'       => 1,
        'pinyin/s'      => '',
        'jianpin/s'     => '',
        'initials/s'    => '',
        'citycode/s'    => '',
        'zipcode/s'     => '',
        'longitude/s'   => '',
        'latitude/s'    => '',
        'remark/s'      => '',
        'sort/d'        => 2250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'region_pid', 'level', 'citycode', 'zipcode', 'longitude', 'latitude'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("trees", ref={Model::class}, type="tree", desc="树形", field="region_id,region_pid,region_name"),
     *   @Apidoc\Returned("levels", type="array", desc="级别name,value"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps   = $exp ? where_exps() : [];
        $trees  = self::list('tree', [where_delete()], [], 'region_name');
        $levels = [
            ['name' => '省', 'value' => 1],
            ['name' => '市', 'value' => 2],
            ['name' => '区县', 'value' => 3],
            ['name' => '街道乡镇', 'value' => 4]
        ];

        return ['exps' => $exps, 'trees' => $trees, 'levels' => $levels];
    }

    /**
     * 地区列表
     * @param string $type  tree树形，list列表
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $param level级别：1省2市3区县4街道乡镇
     * @return array []
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned("list", type="tree", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="region_id,region_pid,region_name,pinyin,citycode,zipcode,longitude,latitude,sort,is_disable"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
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
            $field = $pk . ',' . $pidk . ',region_name,level,pinyin,citycode,zipcode,longitude,latitude,sort,is_disable';
        } else {
            $field = $pk . ',' . $pidk . ',' . $field;
        }

        $level = $param['level'] ?? '';
        if (empty($level)) {
            $level = config('admin.region_level', 3);
        }
        $where_scope = ['level', '<=', $level];
        $param['level'] = $level;

        $cache = self::cache();
        $key   = where_cache_key($type, $where, $order, $field, $page, $limit, $param);
        $data  = $cache->get($key);
        if (empty($data)) {
            $append = [];
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
            $model = model_where($model, $where, $where_scope);
            $data  = $model->append($append)->order($order)->select()->toArray();

            if ($type === 'tree') {
                $data = array_to_tree($data, $pk, $pidk);
            }

            $cache->set($key, $data);
        }

        return $data;
    }

    /**
     * 地区信息
     * @param int  $id   地区id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="region_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned("region_fullname", type="string", desc="地区完整名称")
     * @Apidoc\Returned("region_fullname_py", type="string", desc="地区完整名称拼音")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('地区不存在：') . $id);
                }
                return [];
            }
            $info = $info->toArray();

            // 地区完整名称
            if (strpos($info['path'], ',') === false) {
                $region_fullname    = $info['region_name'];
                $region_fullname_py = $info['pinyin'];
            } else {
                $region_order = 'FIELD(' . $pk . ',' . $info['path'] . ')';
                $region_pids = $model->field('region_name,pinyin')->whereIn($pk, $info['path'])
                    ->orderRaw($region_order)->select()->toArray();
                $region_fullname    = implode('-', array_column($region_pids, 'region_name'));
                $region_fullname_py = implode('-', array_column($region_pids, 'pinyin'));
            }
            $info['region_fullname']    = $region_fullname;
            $info['region_fullname_py'] = $region_fullname_py;

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 地区添加
     * @param array $param 地区信息
     * @Apidoc\Param(ref={Model::class}, withoutField="region_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            $param = self::pinyin($param);
            if (isset($param['region_pid']) && empty($param['region_pid'])) {
                $param['region_pid'] = 0;
            }
            if (isset($param['level']) && empty($param['level'])) {
                $param['level'] = 1;
            }
            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);
                if ($region['is_delete']) {
                    $param['is_delete'] = 1;
                }
                $param['level'] = $region['level'] + 1;
                $model->save($param);
                $region_id = $model->$pk;
                $path = $region['path'] . ',' . $region_id;
            } else {
                $model->save($param);
                $region_id = $model->$pk;
                $path = $region_id;
            }
            $model->where($pk, $region_id)->update(['path' => $path]);
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

        $param[$pk] = $region_id;

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 地区修改
     * @param int|array $ids   地区id
     * @param array     $param 地区信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function edit($ids, $param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (isset($param['region_pid'])) {
                $region_pid = $param['region_pid'];
                $update['region_pid']  = $region_pid;
                $update['update_uid']  = $param['update_uid'];
                $update['update_time'] = $param['update_time'];
                $region = [];
                if ($region_pid) {
                    $region = self::info($region_pid);
                }
                if (is_numeric($ids)) {
                    $ids = [$ids];
                }
                foreach ($ids as $v) {
                    $level = 1;
                    $path  = $v;
                    if ($region) {
                        $level = $region['level'] + 1;
                        $path  = $region['path'] . ',' . $v;
                    }
                    $update['level'] = $level;
                    $update['path']  = $path;
                    $model->where($pk, $v)->update($update);
                }
            } else {
                $param = self::pinyin($param);
                $model->where($pk, 'in', $ids)->update($param);
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
     * 地区删除
     * @param array $ids  地区id
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
     * 地区是否禁用
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
     * 地区批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'region_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 地区导出导入表头
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
            ['field' => 'region_pid', 'name' => lang('上级ID'), 'width' => 12],
            ['field' => 'region_name', 'name' => lang('名称'), 'width' => 26, 'color' => 'FF0000'],
            ['field' => 'path', 'name' => lang('路径'), 'width' => 20],
            ['field' => 'level', 'name' => lang('级别'), 'width' => 8],
            ['field' => 'pinyin', 'name' => lang('拼音'), 'width' => 26],
            ['field' => 'jianpin', 'name' => lang('简拼'), 'width' => 20],
            ['field' => 'initials', 'name' => lang('首字母'), 'width' => 10],
            ['field' => 'citycode', 'name' => lang('区号'), 'width' => 10],
            ['field' => 'zipcode', 'name' => lang('邮编'), 'width' => 10],
            ['field' => 'longitude', 'name' => lang('经度'), 'width' => 12],
            ['field' => 'latitude', 'name' => lang('纬度'), 'width' => 12],
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
     * 地区导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 1;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SETTING_REGION;

        $field = 'region_id,region_pid,region_name,path,level,pinyin,jianpin,initials,citycode,zipcode,longitude,latitude,sort,is_disable,create_time,update_time';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 地区拼音，简拼，首字母
     * @param array $param
     */
    public static function pinyin($param)
    {
        if (empty($param['region_name'] ?? '')) {
            return $param;
        }

        $region_py = Pinyin::sentence($param['region_name'], 'none')->toArray();
        $pinyin    = '';
        $jianpin   = '';
        $initials  = '';

        foreach ($region_py as $k => $v) {
            $region_py_i = '';
            $region_py_e = '';
            $region_py_i = strtoupper(substr($v, 0, 1));
            $region_py_e = substr($v, 1);
            $pinyin  .= $region_py_i . $region_py_e;
            $jianpin .= $region_py_i;
            if ($k == 0) {
                $initials = $region_py_i;
            }
        }

        $param['pinyin']   = $param['pinyin'] ?: $pinyin;
        $param['jianpin']  = $param['jianpin'] ?: $jianpin;
        $param['initials'] = $param['initials'] ?: $initials;

        return $param;
    }
}
