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
     * @Apidoc\Returned("setting", ref="app\common\model\file\SettingModel", type="object", desc="设置信息",
     *   @Apidoc\Returned("accept_ext", type="string", default="", desc="允许上传的文件后缀")
     * )
     * @Apidoc\Returned("storage", type="object", desc="存储方式")
     */
    public function info()
    {
        $data['setting'] = SettingService::info();
        $data['storage'] = SettingService::storages();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel")
     */
    public function edit()
    {
        $param['is_upload_admin']          = $this->request->param('is_upload_admin/d', 1);
        $param['is_upload_api']            = $this->request->param('is_upload_api/d', 1);
        $param['storage']                  = $this->request->param('storage/s', 'local');
        $param['qiniu_access_key']         = $this->request->param('qiniu_access_key/s', '');
        $param['qiniu_secret_key']         = $this->request->param('qiniu_secret_key/s', '');
        $param['qiniu_bucket']             = $this->request->param('qiniu_bucket/s', '');
        $param['qiniu_domain']             = $this->request->param('qiniu_domain/s', '');
        $param['aliyun_access_key_id']     = $this->request->param('aliyun_access_key_id/s', '');
        $param['aliyun_access_key_secret'] = $this->request->param('aliyun_access_key_secret/s', '');
        $param['aliyun_bucket']            = $this->request->param('aliyun_bucket/s', '');
        $param['aliyun_bucket_domain']     = $this->request->param('aliyun_bucket_domain/s', '');
        $param['aliyun_endpoint']          = $this->request->param('aliyun_endpoint/s', '');
        $param['tencent_secret_id']        = $this->request->param('tencent_secret_id/s', '');
        $param['tencent_secret_key']       = $this->request->param('tencent_secret_key/s', '');
        $param['tencent_bucket']           = $this->request->param('tencent_bucket/s', '');
        $param['tencent_region']           = $this->request->param('tencent_region/s', '');
        $param['tencent_domain']           = $this->request->param('tencent_domain/s', '');
        $param['baidu_access_key']         = $this->request->param('baidu_access_key/s', '');
        $param['baidu_secret_key']         = $this->request->param('baidu_secret_key/s', '');
        $param['baidu_bucket']             = $this->request->param('baidu_bucket/s', '');
        $param['baidu_endpoint']           = $this->request->param('baidu_endpoint/s', '');
        $param['baidu_domain']             = $this->request->param('baidu_domain/s', '');
        $param['upyun_service_name']       = $this->request->param('upyun_service_name/s', '');
        $param['upyun_operator_name']      = $this->request->param('upyun_operator_name/s', '');
        $param['upyun_operator_pwd']       = $this->request->param('upyun_operator_pwd/s', '');
        $param['upyun_domain']             = $this->request->param('upyun_domain/s', '');
        $param['aws_access_key_id']        = $this->request->param('aws_access_key_id/s', '');
        $param['aws_secret_access_key']    = $this->request->param('aws_secret_access_key/s', '');
        $param['aws_bucket']               = $this->request->param('aws_bucket/s', '');
        $param['aws_region']               = $this->request->param('aws_region/s', '');
        $param['aws_domain']               = $this->request->param('aws_domain/s', '');
        $param['image_ext']                = $this->request->param('image_ext/s', '');
        $param['image_size']               = $this->request->param('image_size/f', 0);
        $param['video_ext']                = $this->request->param('video_ext/s', '');
        $param['video_size']               = $this->request->param('video_size/f', 0);
        $param['audio_ext']                = $this->request->param('audio_ext/s', '');
        $param['audio_size']               = $this->request->param('audio_size/f', 0);
        $param['word_ext']                 = $this->request->param('word_ext/s', '');
        $param['word_size']                = $this->request->param('word_size/f', 0);
        $param['other_ext']                = $this->request->param('other_ext/s', '');
        $param['other_size']               = $this->request->param('other_size/f', 0);
        $param['limit_max']                = $this->request->param('limit_max/d', 9);

        validate(SettingValidate::class)->scene($param['storage'])->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
