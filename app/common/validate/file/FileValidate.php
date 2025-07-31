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
use app\common\service\file\FileService as Service;
use app\common\model\file\FileModel as Model;
use app\common\service\file\SettingService;
use app\common\model\file\TagsModel;

/**
 * 文件管理验证器
 */
class FileValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'field'       => ['require', 'checkUpdateField'],
        'file'        => ['require', 'file', 'checkLimit'],
        'file_id'     => ['require'],
        'file_type'   => ['require'],
        'file_url'    => ['require', 'url', 'checkFileUrl'],
        'unique'      => ['checkUnique'],
        'import_file' => ['require', 'file', 'fileExt' => 'xlsx'],
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'file.require'        => '请选择上传文件',
        'file_type.require'   => '请选择文件类型',
        'file_url.require'    => '请输入文件链接',
        'file_url.url'        => '请输入有效的文件链接',
        'import_file.require' => '请选择导入文件',
        'import_file.fileExt' => '只允许xlsx文件格式',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'info'        => ['file_id'],
        'add'         => ['file'],
        'addurl'      => ['unique', 'file_type', 'file_url'],
        'edit'        => ['file_id', 'unique'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'update'      => ['ids', 'field'],
        'import'      => ['import_file'],
        'recycleReco' => ['ids'],
        'recycleDele' => ['ids'],
    ];

    /**
     * 验证场景定义：文件删除
     */
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkTagGroup');
    }

    /**
     * 自定义验证规则：编号是否已存在
     */
    protected function checkUnique($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        return true;
    }

    /**
     * 自定义验证规则：文件批量修改字段
     */
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    /**
     * 自定义验证规则：文件限制
     */
    protected function checkLimit($value, $rule, $data = [])
    {
        $file    = $data['file'];
        $setting = SettingService::info();

        $file_ext = strtolower($file->getOriginalExtension());
        if (empty($file_ext)) {
            return lang('上传的文件格式不允许');
        }

        $file_type   = SettingService::fileType($file_ext);
        $set_ext_arr = $setting[$file_type . '_ext'];
        if (is_string($set_ext_arr)) {
            $set_ext_arr = explode(',', $set_ext_arr);
        }
        $set_ext_str = implode(',', $set_ext_arr);
        if (!in_array($file_ext, $set_ext_arr)) {
            return lang('上传的文件格式不允许，允许格式：', ['name' => $set_ext_str]);
        }

        if (!user_upload_limit()) {
            $file_size  = $file->getSize();
            $set_size_m = $setting[$file_type . '_size'];
            $set_size_b = $set_size_m * 1048576;
            if ($file_size > $set_size_b) {
                return lang('上传的文件大小不允许，允许大小：', ['name' =>  $set_size_m . ' MB']);
            }
        }

        return true;
    }

    /**
     * 自定义验证规则：文件是否存在标签或分组
     */
    protected function checkTagGroup($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $where = where_delete([[$pk, 'in', $data['ids']], ['group_id', '>', 0]]);
        $info  = $model::field($pk)->where($where)->find();
        if ($info) {
            // return '文件存在分组，请解除后再删除：' . $info[$pk];
        }

        $info = TagsModel::field($pk)->where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '文件存在标签，请解除后再删除：' . $info[$pk];
        }

        return true;
    }

    /**
     * 自定义验证规则：文件链接
     */
    protected function checkFileUrl($value, $rule, $data = [])
    {
        $file_url  = $data['file_url'];
        $file_info = $this->service::fileInfo($file_url);
        $file_exts = SettingService::fileExts();
        if (!empty($file_info['file_ext']) && !in_array($file_info['file_ext'], $file_exts)) {
            return lang('文件格式不允许：' . $file_info['file_ext']);
        }

        return true;
    }
}
