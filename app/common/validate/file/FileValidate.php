<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\file;

use think\Validate;
use app\common\service\file\SettingService;
use app\common\model\file\TagsModel;
use app\common\model\file\FileModel;

/**
 * 文件管理验证器
 */
class FileValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'file'      => ['require', 'file', 'checkLimit'],
        'file_id'   => ['require'],
        'file_type' => ['require'],
        'file_url'  => ['require', 'url'],
    ];

    // 错误信息
    protected $message = [
        'file.require'      => '请选择上传文件',
        'file_type.require' => '请选择文件类型',
        'file_url.require'  => '请输入文件链接',
        'file_url.url'      => '请输入有效的文件链接',
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['file_id'],
        'add'         => ['file'],
        'addurl'      => ['file_type', 'file_url'],
        'edit'        => ['file_id'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'editgroup'   => ['ids'],
        'edittag'     => ['ids'],
        'edittype'    => ['ids', 'file_type'],
        'editdomain'  => ['ids'],
        'recycleReco' => ['ids'],
        'recycleDele' => ['ids'],
    ];

    // 验证场景定义：文件删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkTagGroup');
    }

    // 自定义验证规则：文件限制
    protected function checkLimit($value, $rule, $data = [])
    {
        $file    = $data['file'];
        $setting = SettingService::info();

        $file_ext = strtolower($file->getOriginalExtension());
        if (empty($file_ext)) {
            return '上传的文件格式不允许';
        }

        $file_type   = SettingService::fileType($file_ext);
        $set_ext_str = $setting[$file_type . '_ext'];
        $set_ext_arr = explode(',', $set_ext_str);
        if (!in_array($file_ext, $set_ext_arr)) {
            return '上传的文件格式不允许，允许格式：' . $set_ext_str;
        }

        $file_size  = $file->getSize();
        $set_size_m = $setting[$file_type . '_size'];
        $set_size_b = $set_size_m * 1048576;
        if ($file_size > $set_size_b) {
            return '上传的文件大小不允许，允许大小：' . $set_size_m . ' MB';
        }

        return true;
    }

    // 自定义验证规则：文件是否存在标签或分组
    protected function checkTagGroup($value, $rule, $data = [])
    {
        // $where = where_delete([['file_id', 'in', $data['ids']], ['group_id', '>', 0]]);
        // $info = FileModel::field('file_id')->where($where)->find();
        // if ($info) {
        //     return '文件存在分组，请解除后再删除：' . $info['file_id'];
        // }

        // $info = TagsModel::field('file_id')->where('file_id', 'in', $data['ids'])->find();
        // if ($info) {
        //     return '文件存在标签，请解除后再删除：' . $info['file_id'];
        // }

        return true;
    }
}
