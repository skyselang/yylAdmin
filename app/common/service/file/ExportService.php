<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\file\ExportCache as Cache;
use app\common\model\file\ExportModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\system\SettingService as SystemSettingService;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;

/**
 * 导出文件
 */
class ExportService
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
        'export_id' => '',
        'remark/s'  => '',
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("types", type="array", desc="类型"),
     *   @Apidoc\Returned("statuss", type="array", desc="状态"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps    = $exp ? where_exps() : [];
        $types   = SettingService::expImpType('', 'export', true);
        $statuss = SettingService::expImpStatus('', true);

        return ['exps' => $exps, 'types' => $types, 'statuss' => $statuss];
    }

    /**
     * 导出文件列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="export_id,type,file_name,file_path,file_size,status,time,export_num,remark,create_uid,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref={Model::class,"getFileUrlAttr"}, field="file_url"),
     *   @Apidoc\Returned(ref={Model::class,"getFileSizeNameAttr"}, field="file_size_name"),
     *   @Apidoc\Returned(ref={Model::class,"getTypeNameAttr"}, field="type_name"),
     *   @Apidoc\Returned(ref={Model::class,"getStatusNameAttr"}, field="status_name"),
     *   @Apidoc\Returned(ref={Model::class,"createUser"}, field="create_uname"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if (empty($where)) {
            $where = where_delete();
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',type,file_name,file_path,file_size,status,time,export_num,remark,is_disable,create_uid,create_time,update_time,delete_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $where_scope = [];
        if (user_hide_where()) {
            $where_scope[] = user_hide_where('create_uid');
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'type')) {
            $append[] = 'type_name';
        }
        if (strpos($field, 'file_path')) {
            $append[] = 'file_url';
        }
        if (strpos($field, 'file_size')) {
            $append[] = 'file_size_name';
        }
        if (strpos($field, 'status')) {
            $append[] = 'status_name';
        }
        if (strpos($field, 'create_uid')) {
            $with[] = $hidden[] = 'createUser';
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
            $count = model_where($model->clone(), $where, $where_scope)->count();
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
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 导出文件信息
     * @param string $id   导出id、编号
     * @param bool   $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="export_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getFileUrlAttr"}, field="file_url")
     * @Apidoc\Returned(ref={Model::class,"getFileSizeNameAttr"}, field="file_size_name")
     * @Apidoc\Returned(ref={Model::class,"getTypeNameAttr"}, field="type_name")
     * @Apidoc\Returned(ref={Model::class,"getStatusNameAttr"}, field="status_name")
     * @Apidoc\Returned(ref={Model::class,"createUser"}, field="create_uname")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();
            $where = [[$pk, '=', $id]];
            $info  = $model->with(['createUser'])->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('导出文件不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['type_name', 'file_url', 'file_size_name', 'status_name'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 导出文件添加
     * @param array $param 文件信息
     * @return int
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        $is_import = $param['is_import'] ?? 0;
        $param['file_path'] = SettingService::expImpFilePath($param['type']);
        $param['file_name'] = SettingService::expImpFileName($param['type'], 'export', $is_import);
        if ($is_import) {
            $param['remark'] = lang('获取导入模板');
        }
        if (!isset($param['create_uid'])) {
            $param['create_uid'] = user_id();
        }
        $param['create_time'] = datetime();
        $model->save($param);

        return $model->$pk;
    }

    /**
     * 导出文件修改
     * @param int|array $ids   导出id
     * @param array     $param 文件信息
     * @Apidoc\Param(ref={Model::class}, field="export_id,remark")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        if (!isset($param['update_uid'])) {
            $param['update_uid'] = user_id();
        }
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $param;
    }

    /**
     * 导出文件删除
     * @param array $ids  导出id
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
                $file = $model->field('file_path')->where($pk, 'in', $ids)->select();
                foreach ($file as $v) {
                    @unlink($v['file_path']); // 删除文件
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
        if ($errmsg ?? '') {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $update;
    }

    /**
     * 导出文件是否禁用
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
     * 导出文件批量修改
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
     * 导出文件导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $type      = $exp_imp == 'export' ? 'type_name' : 'type';
        $file_size = $exp_imp == 'export' ? 'file_size_name' : 'file_size';
        $status    = $exp_imp == 'export' ? 'status_name' : 'status';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => $type, 'name' => lang('类型'), 'width' => 20],
            ['field' => 'file_name', 'name' => lang('名称'), 'width' => 36],
            ['field' => 'file_url', 'name' => lang('文件'), 'width' => 26],
            ['field' => $file_size, 'name' => lang('大小'), 'width' => 10],
            ['field' => 'export_num', 'name' => lang('数量'), 'width' => 10],
            ['field' => $status, 'name' => lang('状态'), 'width' => 10],
            ['field' => 'time', 'name' => lang('耗时（秒）'), 'width' => 12],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
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
     * 导出文件导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_FILE_EXPORT;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 导出
     * @param Service $service     服务
     * @param array   $export_info 导出信息
     * @param string  $field       字段
     * @param int     $limit       分批数量
     */
    public static function exports($service, $export_info, $field = '', $limit = 100000)
    {
        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $export_id = self::add($export_info);
        $export_info = self::info($export_id);
        self::edit($export_id, ['status' => SettingService::EXPIMP_STATUS_PROCESSING]);

        $errmsg = $errcode = '';
        try {
            $default_style = new Style();
            $options = new Options();
            $options->DEFAULT_ROW_STYLE = $default_style;
            $options->SHOULD_USE_INLINE_STRINGS = false;
            $writer = new Writer($options);
            $writer->openToFile($export_info['file_path']);

            $is_import = $export_info['is_import'] ?? 0;
            $header = $service::header('export');
            $header_cell = [];
            foreach ($header as $vh) {
                $header_style = null;
                if ($vh['color'] ?? '') {
                    $header_style = new Style();
                    $header_style->setFontColor($vh['color']);
                }
                $header_cell[] = Cell::fromValue($vh['name'], $header_style);
            }
            $header_row = new Row($header_cell);
            $writer->addRow($header_row);

            $page  = 1;
            $pages = 0;
            $count = 0;
            $limit = $is_import ? 10 : $limit;
            $where = $export_info['param']['where'] ?? [];
            $order = $export_info['param']['order'] ?? [];
            while (true) {
                if ($page == 1) {
                    if ($export_info['is_tree']) {
                        $list  = $service::list('list', $where, $order, $field, $page, $limit);
                    } else {
                        $data  = $service::list($where, $page, $limit, $order, $field);
                        $list  = $data['list'];
                        $pages = $data['pages'];
                    }
                } else {
                    if ($page > $pages || $is_import) {
                        break;
                    }
                    if ($export_info['is_tree']) {
                        $list = $service::list('list', $where, $order, $field, $page, $limit);
                    } else {
                        $list = $service::list($where, $page, $limit, $order, $field, false)['list'];
                    }
                }
                if (empty($list)) {
                    break;
                }

                foreach ($list as $v) {
                    $values = [];
                    foreach ($header as $vh) {
                        $vv = $v[$vh['field']] ?? '';
                        if (is_array($vv)) {
                            $vv = json_encode($vv);
                        }
                        if (mb_strlen($vv) > 32767) {
                            $vvs = str_split($vv, 32767);
                            foreach ($vvs as $val) {
                                $values[] = $val;
                            }
                        } else {
                            $values[] = $vv;
                        }
                    }
                    $values = Row::fromValues($values);
                    $writer->addRow($values);
                }

                $count += count($list);
                self::edit($export_info['export_id'], ['export_num' => $count]);

                $page++;
            }

            foreach ($header as $vh) {
                if ($vh['width'] ?? 0) {
                    $options->setColumnWidth($vh['width'], $vh['index'] + 1);
                }
            }

            $writer->close();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            $errcode = $e->getCode();
        }

        if ($errmsg) {
            $export_edit['status'] = SettingService::EXPIMP_STATUS_FAIL;
        } else {
            $export_edit['status'] = SettingService::EXPIMP_STATUS_SUCCESS;
            $export_edit['file_size'] = filesize(public_path() . $export_info['file_path']);
        }
        $export_edit['time'] = microtime(true) - $time_start;
        self::edit($export_id, $export_edit);

        if ($errmsg) {
            $system_setting = SystemSettingService::info();
            if ($export_edit['time'] >= $system_setting['api_timeout']) {
                throw new \think\Exception($errmsg, $errcode);
            } else {
                exception($errmsg, $errcode);
            }
        }

        $export_info = self::info($export_id);

        return $export_info;
    }
}
