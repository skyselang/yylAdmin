<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\file;

use app\common\BaseController;
use app\common\validate\file\SettingValidate;
use app\common\service\file\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件设置")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("430")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("文件设置信息")
     * @Apidoc\Returned("setting", ref="app\common\model\file\SettingModel\infoReturn", type="object", desc="设置信息")
     * @Apidoc\Returned("storage", type="object", desc="存储方式")
     */
    public function info()
    {
        $data['setting'] = SettingService::info();
        $data['storage'] = SettingService::storage();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\SettingModel\editParam")
     */
    public function edit()
    {
        $param['is_open']                  = $this->param('is_open/d', 1);
        $param['storage']                  = $this->param('storage/s', 'local');
        $param['qiniu_access_key']         = $this->param('qiniu_access_key/s', '');
        $param['qiniu_secret_key']         = $this->param('qiniu_secret_key/s', '');
        $param['qiniu_bucket']             = $this->param('qiniu_bucket/s', '');
        $param['qiniu_domain']             = $this->param('qiniu_domain/s', '');
        $param['aliyun_access_key_id']     = $this->param('aliyun_access_key_id/s', '');
        $param['aliyun_access_key_secret'] = $this->param('aliyun_access_key_secret/s', '');
        $param['aliyun_bucket']            = $this->param('aliyun_bucket/s', '');
        $param['aliyun_bucket_domain']     = $this->param('aliyun_bucket_domain/s', '');
        $param['aliyun_endpoint']          = $this->param('aliyun_endpoint/s', '');
        $param['tencent_secret_id']        = $this->param('tencent_secret_id/s', '');
        $param['tencent_secret_key']       = $this->param('tencent_secret_key/s', '');
        $param['tencent_bucket']           = $this->param('tencent_bucket/s', '');
        $param['tencent_region']           = $this->param('tencent_region/s', '');
        $param['tencent_domain']           = $this->param('tencent_domain/s', '');
        $param['baidu_access_key']         = $this->param('baidu_access_key/s', '');
        $param['baidu_secret_key']         = $this->param('baidu_secret_key/s', '');
        $param['baidu_bucket']             = $this->param('baidu_bucket/s', '');
        $param['baidu_endpoint']           = $this->param('baidu_endpoint/s', '');
        $param['baidu_domain']             = $this->param('baidu_domain/s', '');
        $param['upyun_service_name']       = $this->param('upyun_service_name/s', '');
        $param['upyun_operator_name']      = $this->param('upyun_operator_name/s', '');
        $param['upyun_operator_pwd']       = $this->param('upyun_operator_pwd/s', '');
        $param['upyun_domain']             = $this->param('upyun_domain/s', '');
        $param['s3_access_key_id']         = $this->param('s3_access_key_id/s', '');
        $param['s3_secret_access_key']     = $this->param('s3_secret_access_key/s', '');
        $param['s3_bucket']                = $this->param('s3_bucket/s', '');
        $param['s3_region']                = $this->param('s3_region/s', '');
        $param['s3_domain']                = $this->param('s3_domain/s', '');
        $param['image_ext']                = $this->param('image_ext/s', '');
        $param['image_size']               = $this->param('image_size/f', 0);
        $param['video_ext']                = $this->param('video_ext/s', '');
        $param['video_size']               = $this->param('video_size/f', 0);
        $param['audio_ext']                = $this->param('audio_ext/s', '');
        $param['audio_size']               = $this->param('audio_size/f', 0);
        $param['word_ext']                 = $this->param('word_ext/s', '');
        $param['word_size']                = $this->param('word_size/f', 0);
        $param['other_ext']                = $this->param('other_ext/s', '');
        $param['other_size']               = $this->param('other_size/f', 0);
        $param['limit_max']                = $this->param('limit_max/d', 9);

        validate(SettingValidate::class)->scene($param['storage'])->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
