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
use think\facade\Filesystem;
use app\common\cache\file\ImportCache as Cache;
use app\common\model\file\ImportModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\system\SettingService as SystemSettingService;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options as WriterOptions;
use think\facade\Db;

/**
 * 导入文件
 */
class ImportService
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
     * 修改字段
     */
    public static $editField = [
        'import_id' => '',
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
        $types   = SettingService::expImpType('', 'import', true);
        $statuss = SettingService::expImpStatus('', true);

        return ['exps' => $exps, 'types' => $types, 'statuss' => $statuss];
    }

    /**
     * 导入文件列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="import_id,type,file_name,file_path,file_size,status,time,import_num,success_num,fail_num,remark,create_uid,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref={Model::class,"getFileNameSuccessAttr"}, field="file_name_success"),
     *   @Apidoc\Returned(ref={Model::class,"getFileNameFailAttr"}, field="file_name_fail"),
     *   @Apidoc\Returned(ref={Model::class,"getFilePathSuccessAttr"}, field="file_path_success"),
     *   @Apidoc\Returned(ref={Model::class,"getFilePathFailAttr"}, field="file_path_fail"),
     *   @Apidoc\Returned(ref={Model::class,"getFileUrlAttr"}, field="file_url,file_url_success,file_url_fail"),
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
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',type,file_name,file_path,file_size,status,time,import_num,success_num,fail_num,remark,create_uid,create_time,update_time,delete_time';
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
        if (strpos($field, 'file_name')) {
            $append = array_merge($append, ['file_name_success', 'file_name_fail']);
        }
        if (strpos($field, 'file_path')) {
            $append = array_merge($append, ['file_path_success', 'file_path_fail', 'file_url', 'file_url_success', 'file_url_fail']);
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
     * 导入文件信息
     * @param int  $id   导入id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="import_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getFileNameSuccessAttr"})
     * @Apidoc\Returned(ref={Model::class,"getFileNameFailAttr"})
     * @Apidoc\Returned(ref={Model::class,"getFilePathSuccessAttr"})
     * @Apidoc\Returned(ref={Model::class,"getFilePathFailAttr"})
     * @Apidoc\Returned(ref={Model::class,"getFileUrlAttr"})
     * @Apidoc\Returned(ref={Model::class,"getFileSizeNameAttr"})
     * @Apidoc\Returned(ref={Model::class,"getTypeNameAttr"})
     * @Apidoc\Returned(ref={Model::class,"getStatusNameAttr"})
     * @Apidoc\Returned(ref={Model::class,"createUser"})
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
                    exception(lang('导入文件不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['type_name', 'file_name_success', 'file_name_fail', 'file_path_success', 'file_path_fail', 'file_url', 'file_url_success', 'file_url_fail', 'file_size_name', 'status_name'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 导入文件添加
     * @param array $param 文件信息
     * @return int
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $import_file = $param['import_file'];
        unset($param[$pk], $param['import_file']);

        $param['file_path']   = SettingService::expImpFilePath($param['type'], 'import');
        $param['file_name']   = $import_file->getOriginalName();
        $param['file_size']   = $import_file->getSize();
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $file_path = SettingService::impFilePathSave($param['file_path']);
        Filesystem::disk('public')->putFileAs(SettingService::IMPORT_DIR, $import_file, $file_path);
        $model->save($param);

        return $model->$pk;
    }

    /**
     * 导入文件修改
     * @param int|array $ids   导入id
     * @param array     $param 文件信息
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
     * 导入文件删除
     * @param array $ids  导入id
     * @param bool  $real 是否真实删除
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
                    @unlink($model->getFilePathSuccessAttr(null, $v));
                    @unlink($model->getFilePathFailAttr(null, $v));
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

        $cache = cache();
        $cache->del($ids);

        return $update;
    }

    /**
     * 导入文件是否禁用
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
     * 导入文件批量修改
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
     * 导入文件导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $type = $exp_imp == 'export' ? 'type_name' : 'type';
        $file_size = $exp_imp == 'export' ? 'file_size_name' : 'file_size';
        $status = $exp_imp == 'export' ? 'status_name' : 'status';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => $type, 'name' => lang('类型'), 'width' => 20],
            ['field' => 'file_name', 'name' => lang('名称'), 'width' => 50],
            ['field' => $file_size, 'name' => lang('大小'), 'width' => 10],
            ['field' => 'import_num', 'name' => lang('导入数量'), 'width' => 10],
            ['field' => 'success_num', 'name' => lang('成功数量'), 'width' => 10],
            ['field' => 'fail_num', 'name' => lang('失败数量'), 'width' => 10],
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
     * 导入文件导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_FILE_IMPORT;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 导入
     * @param integer $import_id 导入ID
     * @param string  $service   服务名称
     * @param string  $method    方法名称
     */
    public static function imports($import_id, $service, $method = 'import')
    {
        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $import_info = self::info($import_id);
        self::edit($import_id, ['status' => SettingService::EXPIMP_STATUS_PROCESSING]);

        // 启动事务
        Db::startTrans();
        try {
            $data = $service::$method($import_info);
            // 提交事务
            Db::commit();

            $import_num  = $data['import_num'];
            $success     = $data['success'];
            $fail        = $data['fail'];
            $success_num = count($success);
            $fail_num    = count($fail);
            $header      = $data['header'];
            if ($success_num) {
                $file_path = SettingService::impFilePathSuccess($import_info['file_path']);
                self::importsWriter($header, $file_path, $success);
            }
            if ($fail_num) {
                $file_path = SettingService::impFilePathFail($import_info['file_path']);
                self::importsWriter($header, $file_path, $fail);
            }

            $import_edit['status']      = SettingService::EXPIMP_STATUS_SUCCESS;
            $import_edit['time']        = microtime(true) - $time_start;
            $import_edit['import_num']  = $import_num;
            $import_edit['success_num'] = $success_num;
            $import_edit['fail_num']    = $fail_num;
            self::edit($import_id, $import_edit);

            return [
                'import_num'  => $import_num,
                'success_num' => $success_num,
                'fail_num'    => $fail_num,
                'header'      => $header,
                'success'     => $success,
                'fail'        => $fail
            ];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $errmsg  = $e->getMessage();
            $errcode = $e->getCode();
            $import_edit['status'] = SettingService::EXPIMP_STATUS_FAIL;
            $import_edit['time']   = microtime(true) - $time_start;
            self::edit($import_id, $import_edit);

            $system_setting = SystemSettingService::info();
            if ($import_edit['time'] >= $system_setting['api_timeout']) {
                throw new \think\Exception($errmsg, $errcode);
            } else {
                exception($errmsg, $errcode);
            }
        }
    }

    /**
     * 导入读取表格
     * @param array  $header    表头
     * @param string $file_path 文件路径
     */
    public static function importsReader($header, $file_path)
    {
        $options = new Options();
        $options->SHOULD_FORMAT_DATES = true;
        $reader = new Reader($options);
        $reader->open($file_path);

        $time_index = [];
        foreach ($header as $vh) {
            if (($vh['type'] ?? '') == 'time') {
                $time_index[] = $vh['index'];
            }
        }

        $data = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $temp = $row->toArray();
                foreach ($temp as $cell_index => $cell_value) {
                    if (in_array($cell_index, $time_index) && $cell_value) {
                        if ($cell_value = strtotime($cell_value)) {
                            $cell_value = date('Y-m-d H:i:s', $cell_value);
                            $temp[$cell_index] = $cell_value;
                        }
                    }
                }
                $data[] = $temp;
            }
        }

        $reader->close();
        array_shift($data);

        return $data;
    }

    /**
     * 导入写入表格
     * @param array  $header    表头
     * @param string $file_path 文件路径
     * @param array  $data      数据
     */
    public static function importsWriter($header, $file_path, $data)
    {
        $default_style = new Style();
        $options = new WriterOptions();
        $options->DEFAULT_ROW_STYLE = $default_style;
        $options->SHOULD_USE_INLINE_STRINGS = false;
        $writer = new Writer($options);
        $writer->openToFile($file_path);

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

        foreach ($data as $v) {
            $values = [];
            foreach ($header as $vh) {
                $values[] = $v[$vh['field']] ?? '';
            }
            $values = Row::fromValues($values);
            $writer->addRow($values);
        }

        foreach ($header as $index => $vh) {
            if ($vh['width'] ?? 0) {
                $column = $vh['index'] + 1;
                if ($vh['index'] < 0) {
                    $column = $index + 1;
                }
                $options->setColumnWidth($vh['width'], $column);
            }
        }

        $writer->close();
    }
}
