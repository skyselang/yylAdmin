<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\file;

use think\Model;
use hg\apidoc\annotation as Apidoc;
use app\common\model\system\UserModel;
use app\common\service\file\SettingService;

/**
 * 导入文件模型
 */
class ImportModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'file_import';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'import_id';

    /**
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     * @return string
     */
    public function getTypeNameAttr($value, $data)
    {
        return SettingService::expImpType($data['type'], 'import');
    }

    /**
     * 获取文件名称（成功）
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_name_success", type="string", desc="文件名称（成功）")
     * @return string
     */
    public function getFileNameSuccessAttr($value, $data)
    {
        return substr($data['file_name'], 0, -5) . '-成功.xlsx';
    }

    /**
     * 获取文件名称（失败）
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_name_fail", type="string", desc="文件名称（失败）")
     * @return string
     */
    public function getFileNameFailAttr($value, $data)
    {
        return substr($data['file_name'], 0, -5) . '-失败.xlsx';
    }

    /**
     * 获取文件路径（成功）
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_path_success", type="string", desc="文件路径（成功）")
     * @return string
     */
    public function getFilePathSuccessAttr($value, $data)
    {
        return SettingService::impFilePathSuccess($data['file_path']);
    }

    /**
     * 获取文件路径（失败）
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_path_fail", type="string", desc="文件路径（失败）")
     * @return string
     */
    public function getFilePathFailAttr($value, $data)
    {
        return SettingService::impFilePathFail($data['file_path']);
    }

    /**
     * 获取文件链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_url", type="string", desc="文件链接")
     * @Apidoc\AddField("file_url_success", type="string", desc="文件链接（成功）")
     * @Apidoc\AddField("file_url_fail", type="string", desc="文件链接（失败）")
     * @return string
     */
    public function getFileUrlAttr($value, $data)
    {
        if (empty($data['import_num'])) {
            return '';
        }
        return file_url($data['file_path']);
    }
    public function getFileUrlSuccessAttr($value, $data)
    {
        if (empty($data['success_num'])) {
            return '';
        }
        return file_url($this->getFilePathSuccessAttr($value, $data));
    }
    public function getFileUrlFailAttr($value, $data)
    {
        if (empty($data['fail_num'])) {
            return '';
        }
        return file_url($this->getFilePathFailAttr($value, $data));
    }

    /**
     * 获取文件大小名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_size_name", type="string", desc="文件大小名称")
     * @return string
     */
    public function getFileSizeNameAttr($value, $data)
    {
        return SettingService::fileSize($data['file_size']);
    }

    /**
     * 获取状态名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("status_name", type="string", desc="状态名称")
     * @return string
     */
    public function getStatusNameAttr($value, $data)
    {
        return SettingService::expImpStatus($data['status']);
    }

    /**
     * 关联添加用户
     * @Apidoc\Field("")
     * @Apidoc\AddField("create_uname", type="string", desc="添加用户名称")
     * @return \think\model\relation\HasOne
     */
    public function createUser()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'create_uid')->bind(['create_uname' => 'nickname']);
    }
}
