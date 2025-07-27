<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\file;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\file\SettingValidate as Validate;
use app\common\service\file\SettingService as Service;

/**
 * @Apidoc\Title("lang(文件设置)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("250")
 */
class Setting extends BaseController
{
    /**
     * 验证器
     */
    protected $validate = Validate::class;

    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * @Apidoc\Title("lang(基本设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="is_upload_api,is_upload_admin,is_storage_https,storage,qiniu_access_key,qiniu_secret_key,qiniu_bucket,qiniu_domain,aliyun_access_key_id,aliyun_access_key_secret,aliyun_bucket,aliyun_endpoint,aliyun_domain,tencent_secret_id,tencent_secret_key,tencent_bucket,tencent_region,tencent_domain,baidu_access_key,baidu_secret_key,baidu_bucket,baidu_endpoint,baidu_domain,upyun_service_name,upyun_operator_name,upyun_operator_pwd,upyun_domain,huawei_access_key_id,huawei_secret_access_key,huawei_bucket,huawei_endpoint,huawei_domain,aws_access_key_id,aws_secret_access_key,aws_bucket,aws_region,aws_endpoint,aws_domain")
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function basicInfo()
    {
        $data = $this->service::info('is_upload_api,is_upload_admin,is_storage_https,storage,qiniu_access_key,qiniu_secret_key,qiniu_bucket,qiniu_domain,aliyun_access_key_id,aliyun_access_key_secret,aliyun_bucket,aliyun_endpoint,aliyun_domain,tencent_secret_id,tencent_secret_key,tencent_bucket,tencent_region,tencent_domain,baidu_access_key,baidu_secret_key,baidu_bucket,baidu_endpoint,baidu_domain,upyun_service_name,upyun_operator_name,upyun_operator_pwd,upyun_domain,huawei_access_key_id,huawei_secret_access_key,huawei_bucket,huawei_endpoint,huawei_domain,aws_access_key_id,aws_secret_access_key,aws_bucket,aws_region,aws_endpoint,aws_domain');
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(基本设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="is_upload_api,is_upload_admin,is_storage_https,storage,qiniu_access_key,qiniu_secret_key,qiniu_bucket,qiniu_domain,aliyun_access_key_id,aliyun_access_key_secret,aliyun_bucket,aliyun_endpoint,aliyun_domain,tencent_secret_id,tencent_secret_key,tencent_bucket,tencent_region,tencent_domain,baidu_access_key,baidu_secret_key,baidu_bucket,baidu_endpoint,baidu_domain,upyun_service_name,upyun_operator_name,upyun_operator_pwd,upyun_domain,huawei_access_key_id,huawei_secret_access_key,huawei_bucket,huawei_endpoint,huawei_domain,aws_access_key_id,aws_secret_access_key,aws_bucket,aws_region,aws_endpoint,aws_domain")
     */
    public function basicEdit()
    {
        $param = $this->params([
            'is_upload_api/d'            => 1,
            'is_upload_admin/d'          => 1,
            'is_storage_https/d'         => 1,
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
            'aws_endpoint/s'             => '',
            'aws_domain/s'               => '',
        ]);

        validate($this->validate)->scene($param['storage'])->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(限制设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="image_size,image_ext,video_size,video_ext,audio_size,audio_ext,word_size,word_ext,other_size,other_ext,limit_max,image_exts,video_exts,audio_exts,word_exts,other_exts")
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function limitInfo()
    {
        $data = $this->service::info('image_size,image_ext,video_size,video_ext,audio_size,audio_ext,word_size,word_ext,other_size,other_ext,limit_max,image_exts,video_exts,audio_exts,word_exts,other_exts');
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(限制设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="image_size,image_ext,video_size,video_ext,audio_size,audio_ext,word_size,word_ext,other_size,other_ext,limit_max")
     */
    public function limitEdit()
    {
        $param = $this->params([
            'image_size/f' => 0,
            'image_ext/a'  => [],
            'video_size/f' => 0,
            'video_ext/a'  => [],
            'audio_size/f' => 0,
            'audio_ext/a'  => [],
            'word_size/f'  => 0,
            'word_ext/a'   => [],
            'other_size/f' => 0,
            'other_ext/s'  => '',
            'limit_max/d'  => 9,
        ]);

        validate($this->validate)->scene('limitEdit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(前台设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="is_api_file,api_file_types,api_file_group_ids,api_file_tag_ids")
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function apiInfo()
    {
        $data = $this->service::info('is_api_file,api_file_types,api_file_group_ids,api_file_tag_ids');
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(前台设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="is_api_file,api_file_types,api_file_group_ids,api_file_tag_ids")
     */
    public function apiEdit()
    {
        $param = $this->params([
            'is_api_file/d'        => 0,
            'api_file_types/a'     => [],
            'api_file_group_ids/a' => [],
            'api_file_tag_ids/a'   => [],
        ]);

        validate($this->validate)->scene('apiEdit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }
}
