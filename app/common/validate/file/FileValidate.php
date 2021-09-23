<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件管理验证器
namespace app\common\validate\file;

use think\Validate;
use app\common\service\file\SettingService;
use app\common\service\file\FileService;

class FileValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'file_ids' => ['require', 'array'],
        'file'     => ['require', 'file', 'checkFile'],
        'file_id'  => ['require'],
        'group_id' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'file.require'     => '请选择上传文件',
        'file_id.require'  => '缺少参数：file_id',
        'group_id.require' => '缺少参数：group_id',
    ];

    // 验证场景
    protected $scene = [
        'id'        => ['file_id'],
        'info'      => ['file_id'],
        'add'       => ['file'],
        'edit'      => ['file_id'],
        'dele'      => ['file_ids'],
        'disable'   => ['file_ids'],
        'grouping'  => ['file_ids'],
        'reco_reco' => ['file_ids'],
        'reco_dele' => ['file_ids'],
    ];

    // 自定义验证规则：上传限制
    protected function checkFile($value, $rule, $data = [])
    {
        $file = $data['file'];
        $setting = SettingService::info();
        $file_ext = $file->getOriginalExtension();
        $file_type = FileService::typeJudge($file_ext);

        $set_ext = $setting[$file_type . '_ext'];
        if ($set_ext) {
            $set_ext = explode(',', $set_ext);
            if (!in_array($file_ext, $set_ext)) {
                return '上传的文件类型不允许：' . $file_ext;
            }
        }

        $file_size = $file->getSize();
        $set_size = $setting[$file_type . '_size'];
        if ($set_size) {
            $set_size = $set_size * 1048576;
            if ($file_size > $set_size) {
                $file_size = round($file_size / 1048576, 2);
                return '上传的文件大小不允许：' . $file_size . ' MB';
            }
        }

        return true;
    }
}
