<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use app\common\cache\file\ExportCache;
use app\common\model\file\ExportModel;
use app\common\model\file\ExportModel as FileExportModel;

/**
 * 导出文件
 */
class ExportService
{
    /**
     * 类型：会员导出
     */
    public const TYPE_MEMBER = 10;
    /**
     * 类型：会员标签导出
     */
    public const TYPE_MEMBER_TAG = 11;
    /**
     * 类型：内容导出
     */
    public const TYPE_CONTENT = 20;
    /**
     * 类型：文件导出
     */
    public const TYPE_FILE = 30;
    /**
     * 类型
     *
     * @param  integer $type
     * @return string|array
     */
    public static function types($type = 0)
    {
        $types = [
            self::TYPE_MEMBER     => '会员导出',
            self::TYPE_MEMBER_TAG => '会员标签导出',
            self::TYPE_CONTENT    => '内容导出',
            self::TYPE_FILE       => '文件导出',
        ];

        if ($type !== 0) {
            return $types[$type] ?? '';
        }

        return $types;
    }

    /**
     * 状态：待处理
     */
    public const STATUS_PENDING = 1;
    /**
     * 状态：处理中
     */
    public const STATUS_PROCESSING = 2;
    /**
     * 状态：处理成功
     */
    public const STATUS_SUCCESS = 3;
    /**
     * 状态：处理失败
     */
    public const STATUS_FAIL = 4;
    /**
     * 状态
     *
     * @param  integer $status
     * @return string|array
     */
    public static function statuss($status = 0)
    {
        $statuss = [
            self::STATUS_PENDING    => '待处理',
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_SUCCESS    => '处理成功',
            self::STATUS_FAIL       => '处理失败',
        ];

        if ($status !== 0) {
            return $statuss[$status] ?? '';
        }

        return $statuss;
    }

    /**
     * 导出文件保存目录
     * @var string
     */
    public static $file_dir = 'storage/export/file';
    /**
     * 导出文件保存路径
     * @return string
     */
    public static function filePath()
    {
        $public_path = public_path();
        $file_dir = $public_path . '/' . self::$file_dir;
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0777, true);
        }
        return $public_path . '/';
    }

    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'export_id/d' => '',
        'remark/s'    => '',
    ];

    /**
     * 导出文件列表
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
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
    {
        $model = new ExportModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',type,file_name,file_path,file_size,status,times,remark,create_uid,create_time,update_time,delete_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        if (user_hide_where()) {
            $where[] = user_hide_where('create_uid');
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'type')) {
            $append[] = 'type_name';
        }
        if (strpos($field, 'file_path')) {
            $append[] = 'file_url';
        }
        if (strpos($field, 'file_size')) {
            $append[] = 'file_size';
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
            ->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 导出文件信息
     *
     * @param string $id   导出id、标识
     * @param bool   $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = ExportCache::get($id);
        if (empty($info)) {
            $model = new ExportModel();
            $pk = $model->getPk();
            $where = [[$pk, '=', $id]];
            $info = $model->with(['createUser'])
                ->append(['type_name', 'file_url', 'file_size', 'status_name'])
                ->where($where)->find()->toArray();
            if (empty($info)) {
                if ($exce) {
                    exception('导出文件不存在：' . $id);
                }
                return [];
            }

            ExportCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 导出文件添加
     *
     * @param array $param 文件信息
     * 
     * @return int|Exception
     */
    public static function add($param)
    {
        $model = new ExportModel();
        $pk = $model->getPk();

        unset($param[$pk]);
        if (!isset($param['create_uid'])) {
            $param['create_uid'] = user_id();
        }
        $param['create_time'] = datetime();
        $model->save($param);

        return $model->$pk;
    }

    /**
     * 导出文件修改
     *
     * @param int|array $ids   导出id
     * @param array     $param 文件信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new ExportModel();
        $pk = $model->getPk();

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

        ExportCache::del($ids);

        return $param;
    }

    /**
     * 导出文件删除
     *
     * @param array $ids  导出id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new ExportModel();
        $pk = $model->getPk();

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

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        ExportCache::del($ids);

        return $update;
    }

    /**
     * 文件导出
     * @param array $data
     * @return array|void
     */
    public static function file($data)
    {
        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $export_id = $data['export_id'] ?? 0;
        $export_info = (new FileExportModel)->find($export_id);
        if ($export_info['status'] == ExportService::STATUS_SUCCESS) {
            return;
        }
        if ($export_info['status'] == ExportService::STATUS_FAIL) {
            return;
        }
        ExportService::edit($export_id, ['status' => ExportService::STATUS_PROCESSING]);

        $fields = [
            ['field' => 'unique', 'name' => '标识', 'width' => 16],
            ['field' => 'file_hash', 'name' => 'Hash', 'width' => 16],
            ['field' => 'storage_name', 'name' => '存储', 'width' => 16],
            ['field' => 'file_type_name', 'name' => '类型', 'width' => 8],
            ['field' => 'file_url', 'name' => '文件', 'width' => 20],
            ['field' => 'file_path', 'name' => '路径', 'width' => 20],
            ['field' => 'file_name', 'name' => '名称', 'width' => 36],
            ['field' => 'file_ext', 'name' => '后缀', 'width' => 10],
            ['field' => 'file_size', 'name' => '大小', 'width' => 10],
            ['field' => 'group_name', 'name' => '分组', 'width' => 20],
            ['field' => 'tag_names', 'name' => '标签', 'width' => 20],
            ['field' => 'is_disable_name', 'name' => '禁用', 'width' => 8],
            ['field' => 'sort', 'name' => '排序', 'width' => 10],
            ['field' => 'create_time', 'name' => '添加时间', 'width' => 20],
            ['field' => 'update_time', 'name' => '修改时间', 'width' => 20],
            ['field' => 'file_id', 'name' => 'ID', 'width' => 10],
        ];

        $cell = 'A';
        $row = 2;
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($export_info['file_name']);
        foreach ($fields as $header) {
            $header_cell = $cell++;
            $sheet->setCellValue($header_cell . '1', $header['name']);
            $sheet->getColumnDimension($header_cell)->setWidth($header['width']);
        }

        $page = 1;
        $limit = 10000;
        $where = $export_info['param']['where'] ?? [];
        $order = $export_info['param']['order'] ?? [];
        $field = 'unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_ext,file_size,sort,is_disable,create_time,update_time';
        while (true) {
            $list = FileService::list($where, $page, $limit, $order, $field, false)['list'];
            if (empty($list)) {
                break;
            }
            foreach ($list as $v) {
                $cell = 'A';
                $rows = $row++;
                foreach ($fields as $vf) {
                    $cells = $cell++;
                    $cell_val = $v[$vf['field']] ?? '';
                    $sheet->setCellValue($cells . $rows, $cell_val);
                }
            }
            $page++;
        }

        $file_paths = self::filePath() . $export_info['file_path'];
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setIncludeCharts(false);
        $writer->save($file_paths);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $export_edit = [
            'file_size' => filesize($file_paths),
            'status'    => ExportService::STATUS_SUCCESS,
            'times'     => microtime(true) - $time_start,
        ];
        ExportService::edit($export_id, $export_edit);

        return $export_info;
    }
}
