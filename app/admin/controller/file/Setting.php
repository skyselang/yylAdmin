<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\file;

use app\common\controller\BaseController;
use app\common\validate\file\SettingValidate;
use app\common\service\file\SettingService;
use app\common\service\file\GroupService;
use app\common\service\file\TagService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件设置")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("400")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("文件设置信息")
     * @Apidoc\Returned("setting", type="object", children={
     *   @Apidoc\Returned(ref="app\common\model\file\SettingModel"),
     *   @Apidoc\Returned(ref="app\common\service\file\SettingService\info"),
     * })
     * @Apidoc\Returned("group", type="array", ref="app\common\model\file\GroupModel", field="group_id,group_name", desc="分组列表")
     * @Apidoc\Returned("tag", type="array", ref="app\common\model\file\TagModel", field="tag_id,tag_name", desc="标签列表")
     */
    public function info()
    {
        $data = SettingService::info();

        $data['group'] = GroupService::list([where_delete()], 0, 0, [], 'group_id,group_name')['list'] ?? [];
        $data['tag']   = TagService::list([where_delete()], 0, 0, [], 'tag_id,tag_name')['list'] ?? [];

        return success($data);
    }

    /**
     * @Apidoc\Title("文件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\SettingModel", withoutField="setting_id,create_uid,update_uid,create_time,update_time")
     */
    public function edit()
    {
        $param = $this->params([
            'is_upload_admin/d'          => 1,
            'is_upload_api/d'            => 1,
            'storage/s'                  => 'local',
            'qiniu_access_key/s'         => '',
            'qiniu_secret_key/s'         => '',
            'qiniu_bucket/s'             => '',
            'qiniu_domain/s'             => '',
            'aliyun_access_key_id/s'     => '',
            'aliyun_access_key_secret/s' => '',
            'aliyun_bucket/s'            => '',
            'aliyun_endpoint/s'          => '',
            'aliyun_domain/s'            => '',
            'tencent_secret_id/s'        => '',
            'tencent_secret_key/s'       => '',
            'tencent_bucket/s'           => '',
            'tencent_region/s'           => '',
            'tencent_domain/s'           => '',
            'baidu_access_key/s'         => '',
            'baidu_secret_key/s'         => '',
            'baidu_bucket/s'             => '',
            'baidu_endpoint/s'           => '',
            'baidu_domain/s'             => '',
            'upyun_service_name/s'       => '',
            'upyun_operator_name/s'      => '',
            'upyun_operator_pwd/s'       => '',
            'upyun_domain/s'             => '',
            'huawei_access_key_id/s'     => '',
            'huawei_secret_access_key/s' => '',
            'huawei_bucket/s'            => '',
            'huawei_endpoint/s'          => '',
            'huawei_domain/s'            => '',
            'aws_access_key_id/s'        => '',
            'aws_secret_access_key/s'    => '',
            'aws_bucket/s'               => '',
            'aws_region/s'               => '',
            'aws_domain/s'               => '',
            'image_ext/s'                => '',
            'image_size/f'               => 0,
            'video_ext/s'                => '',
            'video_size/f'               => 0,
            'audio_ext/s'                => '',
            'audio_size/f'               => 0,
            'word_ext/s'                 => '',
            'word_size/f'                => 0,
            'other_ext/s'                => '',
            'other_size/f'               => 0,
            'limit_max/d'                => 9,
            'is_api_file/d'              => 0,
            'api_file_types/a'           => [],
            'api_file_group_ids/a'       => [],
            'api_file_tag_ids/a'         => [],
        ]);

        validate(SettingValidate::class)->scene($param['storage'])->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
