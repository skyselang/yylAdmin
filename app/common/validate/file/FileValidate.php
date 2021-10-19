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
        'file'     => ['require', 'file', 'checkLimit'],
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
    protected function checkLimit($value, $rule, $data = [])
    {
        $file = $data['file'];
        $setting = SettingService::info();

        $file_ext = $file->getOriginalExtension();
        $file_type = FileService::typeJudge($file_ext);
        $set_ext_str = $setting[$file_type . '_ext'];
        $set_ext_arr = explode(',', $set_ext_str);
        if (!in_array($file_ext, $set_ext_arr)) {
            return '上传的文件格式不允许，允许格式：' . $set_ext_str;
        }

        $file_size = $file->getSize();
        $set_size_m = $setting[$file_type . '_size'];
        $set_size_b = $set_size_m * 1048576;
        if ($file_size > $set_size_b) {
            return '上传的文件大小不允许，允许大小：<= ' . $set_size_m . ' MB';
        }

        return true;
    }
}
