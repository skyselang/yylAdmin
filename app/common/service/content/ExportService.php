<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\content;

use app\common\service\file\ExportService as FileExportService;
use app\common\model\file\ExportModel as FileExportModel;

/**
 * 内容导出
 */
class ExportService
{
    /**
     * 导出文件保存目录
     * @var string
     */
    public static $file_dir = 'storage/export/content';
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
     * 内容导出
     * @param array $data
     * @return array|void
     */
    public static function content($data)
    {
        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $export_id = $data['export_id'] ?? 0;
        $export_info = (new FileExportModel)->find($export_id);
        if ($export_info['status'] == FileExportService::STATUS_SUCCESS) {
            return;
        }
        if ($export_info['status'] == FileExportService::STATUS_FAIL) {
            return;
        }
        FileExportService::edit($export_id, ['status' => FileExportService::STATUS_PROCESSING]);

        $fields = [
            ['field' => 'unique', 'name' => '标识', 'width' => 18],
            ['field' => 'image_url', 'name' => '图片', 'width' => 30],
            ['field' => 'category_names', 'name' => '分类', 'width' => 20],
            ['field' => 'tag_names', 'name' => '标签', 'width' => 20],
            ['field' => 'is_top_name', 'name' => '置顶', 'width' => 8],
            ['field' => 'is_hot_name', 'name' => '热门', 'width' => 8],
            ['field' => 'is_rec_name', 'name' => '推荐', 'width' => 8],
            ['field' => 'is_disable_name', 'name' => '禁用', 'width' => 8],
            ['field' => 'hits_show', 'name' => '点击', 'width' => 10],
            ['field' => 'content', 'name' => '内容', 'width' => 20],
            ['field' => 'release_time', 'name' => '发布时间', 'width' => 20],
            ['field' => 'create_time', 'name' => '添加时间', 'width' => 20],
            ['field' => 'update_time', 'name' => '修改时间', 'width' => 20],
            ['field' => 'content_id', 'name' => 'ID', 'width' => 10],
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
        $field = 'unique,image_id,name,release_time,content,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time';
        while (true) {
            $list = ContentService::list($where, $page, $limit, $order, $field, false)['list'];
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
            'status'    => FileExportService::STATUS_SUCCESS,
            'times'     => microtime(true) - $time_start,
        ];
        FileExportService::edit($export_id, $export_edit);

        return $export_info;
    }
}
