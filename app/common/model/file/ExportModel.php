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
 * 导出文件模型
 */
class ExportModel extends Model
{
    // 表名
    protected $name = 'file_export';
    // 表主键
    protected $pk = 'export_id';

    /**
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     */
    public function getTypeNameAttr($value, $data)
    {
        return SettingService::expImpType($data['type'], 'export');
    }

    /**
     * 获取文件链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_url", type="string", desc="文件链接")
     */
    public function getFileUrlAttr($value, $data)
    {
        return file_url($data['file_path']);
    }

    /**
     * 获取文件大小名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_size_name", type="string", desc="文件大小名称")
     */
    public function getFileSizeNameAttr($value, $data)
    {
        return SettingService::fileSize($data['file_size']);
    }

    /**
     * 获取状态名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("status_name", type="string", desc="状态名称")
     */
    public function getStatusNameAttr($value, $data)
    {
        return SettingService::expImpStatus($data['status']);
    }

    /**
     * 修改参数
     */
    public function setParamAttr($value)
    {
        return json_encode($value);
    }
    /**
     * 获取参数
     */
    public function getParamAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 关联添加用户
     * @Apidoc\Field("")
     * @Apidoc\AddField("create_uname", type="string", desc="添加用户名称")
     */
    public function createUser()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'create_uid')->bind(['create_uname' => 'nickname']);
    }
}
