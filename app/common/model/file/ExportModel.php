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
use app\common\service\file\ExportService;
use hg\apidoc\annotation as Apidoc;

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
        $types = ExportService::types();
        return $types[$data['type']] ?? '';
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
        $statuss = ExportService::statuss();
        return $statuss[$data['status']] ?? '';
    }

    /**
     * 获取参数
     */
    public function getParamAttr($value)
    {
        return unserialize($value);
    }
    public function setParamAttr($value)
    {
        return serialize($value);
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
