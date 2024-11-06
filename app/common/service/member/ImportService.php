<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Db;
use app\common\model\file\ImportModel as FileImportModel;
use app\common\service\file\ImportService as FileImportService;

/**
 * 会员导入
 */
class ImportService
{
    /**
     * 导入文件保存目录
     * @var string
     */
    public static $file_dir = 'import/member';
    /**
     * 导入文件保存路径
     * @return string
     */
    public static function filePath()
    {
        $public_path = public_path();
        $file_dir = $public_path . '/storage/' . self::$file_dir;
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0777, true);
        }

        return $public_path . '/';
    }

    /**
     * 会员导入
     * @param array $data
     * @param bool $is_tpl 是否返回模板
     * @return array|void
     */
    public static function member($data, $is_tpl = false)
    {
        if ($is_tpl) {
            return [
                'file_tpl_path' => app()->getRootPath() . 'private/import/member-import.xlsx',
                'file_tpl_name' => '会员导入文件模板',
            ];
        }

        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $import_id = $data['import_id'] ?? 0;
        $import_info = (new FileImportModel)->find($import_id);
        if ($import_info['status'] == FileImportService::STATUS_SUCCESS) {
            return;
        }
        if ($import_info['status'] == FileImportService::STATUS_FAIL) {
            return;
        }
        FileImportService::edit($import_id, ['status' => FileImportService::STATUS_PROCESSING]);

        $file_paths = self::filePath() . $import_info['file_path'];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file_paths);
        $worksheet = $spreadsheet->getSheet(0);
        $max_data_column = $worksheet->getHighestDataColumn();
        $row_iterator = $worksheet->getRowIterator(2, $worksheet->getHighestDataRow());
        $spreadsheet_date = new \PhpOffice\PhpSpreadsheet\Shared\Date;
        $import = [];
        foreach ($row_iterator as $row) {
            $cell_iterator = $row->getCellIterator('A', $max_data_column);
            $row_value = [];
            $row_index = $row->getRowIndex();
            $cell_index = 0;
            foreach ($cell_iterator as $cell) {
                $cell_value = $cell->getValue();
                if ($cell_value && $cell_index == 6 && is_float($cell_value)) {
                    $cell_value = $spreadsheet_date::excelToTimestamp($cell_value);
                    $cell_value = date('Y-m-d H:i:s', $cell_value);
                }
                $row_value[] = $cell_value;
                $cell_index++;
            }
            $import[$row_index] = $row_value;
        }
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $fields = [
            ['index' => 0, 'field' => 'nickname', 'name' => '昵称', 'width' => 24],
            ['index' => 1, 'field' => 'username', 'name' => '用户名', 'width' => 24],
            ['index' => 2, 'field' => 'phone', 'name' => '手机', 'width' => 14],
            ['index' => 3, 'field' => 'email', 'name' => '邮箱', 'width' => 32],
            ['index' => 4, 'field' => 'is_super', 'name' => '是否超会', 'width' => 10],
            ['index' => 5, 'field' => 'is_disable', 'name' => '是否禁用', 'width' => 10],
            ['index' => 6, 'field' => 'create_time', 'name' => '注册时间', 'width' => 20],
            ['index' => -1, 'field' => 'result_msg', 'name' => '导入结果', 'width' => 80],
        ];

        $import_num = count($import);
        $success = $fail = [];
        $batch_num = 10000;
        while (count($import) > 0) {
            $import_batch = array_splice($import, 0, $batch_num);
            foreach ($import_batch as $kib => $vib) {
                $import_temp = [];
                foreach ($fields as $vf) {
                    if ($vf['index'] != -1) {
                        $import_temp[$vf['field']] = $vib[$vf['index']] ?? '';
                    }
                }
                $import_batch[$kib] = $import_temp;
            }

            $usernames = array_column($import_batch, 'username');
            $phones = array_column($import_batch, 'phone');
            $emails = array_column($import_batch, 'email');
            $usernames = Db::name('member')->where('member_id', '>', 0)->where('username', 'in', $usernames)
                ->where('is_delete', 0)->column('username');
            $phones = Db::name('member')->where('member_id', '>', 0)->where('phone', 'in', $phones)
                ->where('is_delete', 0)->column('phone');
            $emails = Db::name('member')->where('member_id', '>', 0)->where('email', 'in', $emails)
                ->where('is_delete', 0)->column('email');

            $success_batch = [];
            foreach ($import_batch as $vib) {
                $vib['result_msg'] = [];
                if ($vib['nickname'] && strlen($vib['nickname']) > 64) {
                    $vib['result_msg'][] = '昵称长度为1-64位';
                }
                if ($vib['username']) {
                    if (strlen($vib['username']) < 2 || strlen($vib['username']) > 64) {
                        $vib['result_msg'][] = '用户名长度为2-64位';
                    } elseif (in_array($vib['username'], $usernames)) {
                        $vib['result_msg'][] = '用户名已存在';
                    }
                } else {
                    $vib['result_msg'][] = '用户名不能为空';
                }
                if ($vib['phone']) {
                    if (!preg_match('/^1[3-9]\d{9}$/', $vib['phone'])) {
                        $vib['result_msg'][] = '手机格式错误';
                    } elseif (in_array($vib['phone'], $phones)) {
                        $vib['result_msg'][] = '手机已存在';
                    }
                }
                if ($vib['email']) {
                    if (!filter_var($vib['email'], FILTER_VALIDATE_EMAIL)) {
                        $vib['result_msg'][] = '邮箱格式错误';
                    } elseif (in_array($vib['email'], $emails)) {
                        $vib['result_msg'][] = '邮箱已存在';
                    }
                }
                if ($vib['create_time']) {
                    if (!strtotime($vib['create_time'])) {
                        $vib['result_msg'][] = '注册时间格式错误';
                    } else {
                        $vib['create_time'] = date('Y-m-d H:i:s', strtotime($vib['create_time']));
                    }
                }

                if ($vib['result_msg']) {
                    $vib['result_msg'] = '失败：' . implode('，', $vib['result_msg']);
                    $fail[] = $vib;
                } else {
                    $vib['result_msg'] = '成功';
                    $success[] = $vib;
                    $success_batch[] = $vib;
                }
            }
            unset($import_batch, $usernames, $phones, $emails);

            if ($success_batch) {
                $field = [];
                foreach ($fields as $vf) {
                    if ($vf['index'] != -1) {
                        $field[] = $vf['field'];
                    }
                }
                $create_time = datetime();
                $insert_all_sql = 'INSERT INTO ya_member (' . implode(',', $field) . ') VALUES ';
                foreach ($success_batch as $vsb) {
                    $success_temp = $vsb;
                    $success_temp['is_super'] = 0;
                    if ($vsb['is_super'] == '是') {
                        $success_temp['is_super'] = 1;
                    }
                    $success_temp['is_disable'] = 0;
                    if ($vsb['is_disable'] == '是') {
                        $success_temp['is_disable'] = 1;
                    }
                    $success_temp['create_time'] = $create_time;
                    if ($vsb['create_time']) {
                        $success_temp['create_time'] = date('Y-m-d H:i:s', strtotime($vsb['create_time']));
                    }
                    $success_temp_sql = [];
                    foreach ($field as $vf) {
                        $success_temp_sql[] = "'" . $success_temp[$vf] . "'";
                    }
                    $insert_all_sql .= '(' . implode(',', $success_temp_sql) . '),';
                }
                unset($success_batch);
                $insert_all_sql = rtrim($insert_all_sql, ',');
                Db::query($insert_all_sql);
                unset($insert_all_sql);
            }
        }

        $file_paths_success = self::filePath() . FileImportService::filePathSuccess($import_info['file_path']);
        $file_paths_fail = self::filePath() . FileImportService::filePathFail($import_info['file_path']);
        $success_num = count($success);
        $fail_num = count($fail);
        $result = [];
        if ($success_num) {
            $result[] = ['title' => '成功', 'data' => $success, 'file_path' => $file_paths_success];
        }
        if ($fail_num) {
            $result[] = ['title' => '失败', 'data' => $fail, 'file_path' => $file_paths_fail];
        }
        foreach ($result as $res) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($res['title']);
            $cell = 'A';
            foreach ($fields as $header) {
                $cells = $cell++;
                $sheet->setCellValue($cells . '1', $header['name']);
                $sheet->getColumnDimension($cells)->setWidth($header['width']);
            }
            $row = 2;
            foreach ($res['data'] as $vr) {
                $cell = 'A';
                $rows = $row++;
                foreach ($fields as $field) {
                    $cells = $cell++;
                    $cell_val = $vr[$field['field']] ?? '';
                    $sheet->setCellValue($cells . $rows, $cell_val);
                }
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setIncludeCharts(false);
            $writer->save($res['file_path']);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        $import_edit['status']      = FileImportService::STATUS_SUCCESS;
        $import_edit['times']       = microtime(true) - $time_start;
        $import_edit['import_num']  = $import_num;
        $import_edit['success_num'] = $success_num;
        $import_edit['fail_num']    = $fail_num;
        FileImportService::edit($import_id, $import_edit);

        return [
            'import_num'  => $import_num,
            'success_num' => $success_num,
            'fail_num'    => $fail_num,
            'success'     => $success,
            'fail'        => $fail,
        ];
    }

    /**
     * 会员标签导入
     * @param array $data
     * @param bool $is_tpl 是否返回模板
     * @return array|void
     */
    public static function memberTag($data, $is_tpl = false)
    {
        if ($is_tpl) {
            return [
                'file_tpl_path' => app()->getRootPath() . 'private/import/member-tag-import.xlsx',
                'file_tpl_name' => '会员标签导入文件模板',
            ];
        }

        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $import_id = $data['import_id'] ?? 0;
        $import_info = (new FileImportModel)->find($import_id);
        if ($import_info['status'] == FileImportService::STATUS_SUCCESS) {
            return;
        }
        if ($import_info['status'] == FileImportService::STATUS_FAIL) {
            return;
        }
        FileImportService::edit($import_id, ['status' => FileImportService::STATUS_PROCESSING]);

        $file_paths = self::filePath() . $import_info['file_path'];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file_paths);
        $worksheet = $spreadsheet->getSheet(0);
        $max_data_column = $worksheet->getHighestDataColumn();
        $row_iterator = $worksheet->getRowIterator(2, $worksheet->getHighestDataRow());
        $spreadsheet_date = new \PhpOffice\PhpSpreadsheet\Shared\Date;
        $import = [];
        foreach ($row_iterator as $row) {
            $cell_iterator = $row->getCellIterator('A', $max_data_column);
            $row_value = [];
            $row_index = $row->getRowIndex();
            $cell_index = 0;
            foreach ($cell_iterator as $cell) {
                $cell_value = $cell->getValue();
                if ($cell_value && $cell_index == 5 && is_float($cell_value)) {
                    $cell_value = $spreadsheet_date::excelToTimestamp($cell_value);
                    $cell_value = date('Y-m-d H:i:s', $cell_value);
                }
                $row_value[] = $cell_value;
                $cell_index++;
            }
            $import[$row_index] = $row_value;
        }
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $fields = [
            ['index' => 0, 'field' => 'tag_name', 'name' => '名称', 'width' => 24],
            ['index' => 1, 'field' => 'tag_desc', 'name' => '描述', 'width' => 30],
            ['index' => 2, 'field' => 'remark', 'name' => '备注', 'width' => 30],
            ['index' => 3, 'field' => 'sort', 'name' => '排序', 'width' => 10],
            ['index' => 4, 'field' => 'is_disable', 'name' => '禁用', 'width' => 10],
            ['index' => 5, 'field' => 'create_time', 'name' => '添加时间', 'width' => 20],
            ['index' => -1, 'field' => 'result_msg', 'name' => '导入结果', 'width' => 80],
        ];

        $import_num = count($import);
        $success = $fail = [];
        $batch_num = 10000;
        while (count($import) > 0) {
            $import_batch = array_splice($import, 0, $batch_num);
            foreach ($import_batch as $kib => $vib) {
                $import_temp = [];
                foreach ($fields as $vf) {
                    if ($vf['index'] != -1) {
                        $import_temp[$vf['field']] = $vib[$vf['index']] ?? '';
                    }
                }
                $import_batch[$kib] = $import_temp;
            }

            $tag_names = array_column($import_batch, 'tag_name');
            $tag_names = Db::name('member_tag')->where('tag_id', '>', 0)->where('tag_name', 'in', $tag_names)
                ->where('is_delete', 0)->column('tag_name');

            $success_batch = [];
            foreach ($import_batch as $vib) {
                $vib['result_msg'] = [];
                if ($vib['tag_name']) {
                    if (in_array($vib['tag_name'], $tag_names)) {
                        $vib['result_msg'][] = '名称已存在';
                    }
                } else {
                    $vib['result_msg'][] = '名称不能为空';
                }
                if ($vib['create_time']) {
                    if (!strtotime($vib['create_time'])) {
                        $vib['result_msg'][] = '添加时间格式错误';
                    } else {
                        $vib['create_time'] = date('Y-m-d H:i:s', strtotime($vib['create_time']));
                    }
                }

                if ($vib['result_msg']) {
                    $vib['result_msg'] = '失败：' . implode('，', $vib['result_msg']);
                    $fail[] = $vib;
                } else {
                    $vib['result_msg'] = '成功';
                    $success[] = $vib;
                    $success_batch[] = $vib;
                }
            }
            unset($import_batch, $tag_names);

            if ($success_batch) {
                $field = [];
                foreach ($fields as $vf) {
                    if ($vf['index'] != -1) {
                        $field[] = $vf['field'];
                    }
                }
                $create_time = datetime();
                $insert_all_sql = 'INSERT INTO ya_member_tag (' . implode(',', $field) . ') VALUES ';
                foreach ($success_batch as $vsb) {
                    $success_temp = $vsb;
                    $success_temp['is_disable'] = 0;
                    if ($vsb['is_disable'] == '是') {
                        $success_temp['is_disable'] = 1;
                    }
                    $success_temp['create_time'] = $create_time;
                    if ($vsb['create_time']) {
                        $success_temp['create_time'] = date('Y-m-d H:i:s', strtotime($vsb['create_time']));
                    }
                    $success_temp_sql = [];
                    foreach ($field as $vf) {
                        $success_temp_sql[] = "'" . $success_temp[$vf] . "'";
                    }
                    $insert_all_sql .= '(' . implode(',', $success_temp_sql) . '),';
                }
                unset($success_batch);
                $insert_all_sql = rtrim($insert_all_sql, ',');
                Db::query($insert_all_sql);
                unset($insert_all_sql);
            }
        }

        $file_paths_success = self::filePath() . FileImportService::filePathSuccess($import_info['file_path']);
        $file_paths_fail = self::filePath() . FileImportService::filePathFail($import_info['file_path']);
        $success_num = count($success);
        $fail_num = count($fail);
        $result = [];
        if ($success_num) {
            $result[] = ['title' => '成功', 'data' => $success, 'file_path' => $file_paths_success];
        }
        if ($fail_num) {
            $result[] = ['title' => '失败', 'data' => $fail, 'file_path' => $file_paths_fail];
        }
        foreach ($result as $res) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($res['title']);
            $cell = 'A';
            foreach ($fields as $header) {
                $cells = $cell++;
                $sheet->setCellValue($cells . '1', $header['name']);
                $sheet->getColumnDimension($cells)->setWidth($header['width']);
            }
            $row = 2;
            foreach ($res['data'] as $vr) {
                $cell = 'A';
                $rows = $row++;
                foreach ($fields as $field) {
                    $cells = $cell++;
                    $sheet->setCellValue($cells . $rows, $vr[$field['field']] ?? '');
                }
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setIncludeCharts(false);
            $writer->save($res['file_path']);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        $import_edit['status']      = FileImportService::STATUS_SUCCESS;
        $import_edit['times']       = microtime(true) - $time_start;
        $import_edit['import_num']  = $import_num;
        $import_edit['success_num'] = $success_num;
        $import_edit['fail_num']    = $fail_num;
        FileImportService::edit($import_id, $import_edit);

        return [
            'import_num'  => $import_num,
            'success_num' => $success_num,
            'fail_num'    => $fail_num,
            'success'     => $success,
            'fail'        => $fail,
        ];
    }
}
