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
use app\common\model\system\UserModel;
use app\common\service\file\SettingService;
use app\common\service\file\ImportService;
use hg\apidoc\annotation as Apidoc;

/**
 * 导入文件模型
 */
class ImportModel extends Model
{
    // 表名
    protected $name = 'file_import';
    // 表主键
    protected $pk = 'import_id';

    /**
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     */
    public function getTypeNameAttr($value, $data)
    {
        $types = ImportService::types();
        return $types[$data['type']] ?? '';
    }

    /**
     * 获取文件名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_name_success", type="string", desc="文件名称（成功）")
     * @Apidoc\AddField("file_name_fail", type="string", desc="文件名称（失败）")
     */
    public function getFileNameSuccessAttr($value, $data)
    {
        return substr($data['file_name'], 0, -5) . '-成功.xlsx';
    }
    public function getFileNameFailAttr($value, $data)
    {
        return substr($data['file_name'], 0, -5) . '-失败.xlsx';
    }

    /**
     * 获取文件路径
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_path_success", type="string", desc="文件路径（成功）")
     * @Apidoc\AddField("file_path_fail", type="string", desc="文件路径（失败）")
     */
    public function getFilePathSuccessAttr($value, $data)
    {
        return ImportService::filePathSuccess($data['file_path']);
    }
    public function getFilePathFailAttr($value, $data)
    {
        return ImportService::filePathFail($data['file_path']);
    }

    /**
     * 获取文件链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_url", type="string", desc="文件链接")
     * @Apidoc\AddField("file_success_url", type="string", desc="文件链接（成功）")
     * @Apidoc\AddField("file_fail_url", type="string", desc="文件链接（失败）")
     */
    public function getFileUrlAttr($value, $data)
    {
        return file_url($data['file_path']);
    }
    public function getFileUrlSuccessAttr($value, $data)
    {
        return file_url($this->getFilePathSuccessAttr($value, $data));
    }
    public function getFileUrlFailAttr($value, $data)
    {
        return file_url($this->getFilePathFailAttr($value, $data));
    }

    /**
     * 获取文件大小
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_size", type="string", desc="文件大小")
     */
    public function getFileSizeAttr($value, $data)
    {
        return SettingService::fileSize($data['file_size']);
    }

    /**
     * 获取状态名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("status_name", status="string", desc="状态名称")
     */
    public function getStatusNameAttr($value, $data)
    {
        $statuss = ImportService::statuss();
        return $statuss[$data['status']] ?? '';
    }

    /**
     * 关联提交人
     * @Apidoc\Field("")
     * @Apidoc\AddField("create_name", type="string", desc="提交人")
     */
    public function createUser()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'create_uid')->bind(['create_name' => 'nickname']);
    }
}
