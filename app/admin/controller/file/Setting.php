<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件设置控制器
namespace app\admin\controller\file;

use think\facade\Request;
use app\common\validate\file\SettingValidate;
use app\common\service\file\SettingService;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件设置")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("430")
 */
class Setting
{
    /**
     * @Apidoc\Title("文件设置信息")
     * @Apidoc\Returned(ref="app\common\model\file\SettingModel\infoReturn")
     */
    public function info()
    {
        $data['setting'] = SettingService::info();
        $data['storage'] = FileService::storage();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\SettingModel\editParam")
     */
    public function edit()
    {
        $param['storage']                  = Request::param('storage/s', 'local');
        $param['qiniu_access_key']         = Request::param('qiniu_access_key/s', '');
        $param['qiniu_secret_key']         = Request::param('qiniu_secret_key/s', '');
        $param['qiniu_bucket']             = Request::param('qiniu_bucket/s', '');
        $param['qiniu_domain']             = Request::param('qiniu_domain/s', '');
        $param['aliyun_access_key_id']     = Request::param('aliyun_access_key_id/s', '');
        $param['aliyun_access_key_secret'] = Request::param('aliyun_access_key_secret/s', '');
        $param['aliyun_bucket']            = Request::param('aliyun_bucket/s', '');
        $param['aliyun_endpoint']          = Request::param('aliyun_endpoint/s', '');
        $param['aliyun_bucket_domain']     = Request::param('aliyun_bucket_domain/s', '');
        $param['tencent_secret_id']        = Request::param('tencent_secret_id/s', '');
        $param['tencent_secret_key']       = Request::param('tencent_secret_key/s', '');
        $param['tencent_bucket']           = Request::param('tencent_bucket/s', '');
        $param['tencent_region']           = Request::param('tencent_region/s', '');
        $param['tencent_domain']           = Request::param('tencent_domain/s', '');
        $param['baidu_access_key']         = Request::param('baidu_access_key/s', '');
        $param['baidu_secret_key']         = Request::param('baidu_secret_key/s', '');
        $param['baidu_bucket']             = Request::param('baidu_bucket/s', '');
        $param['baidu_endpoint']           = Request::param('baidu_endpoint/s', '');
        $param['baidu_domain']             = Request::param('baidu_domain/s', '');
        $param['image_ext']                = Request::param('image_ext/s', '');
        $param['image_size']               = Request::param('image_size/s', 0);
        $param['video_ext']                = Request::param('video_ext/s', '');
        $param['video_size']               = Request::param('video_size/s', 0);
        $param['audio_ext']                = Request::param('audio_ext/s', '');
        $param['audio_size']               = Request::param('audio_size/s', 0);
        $param['word_ext']                 = Request::param('word_ext/s', '');
        $param['word_size']                = Request::param('word_size/s', 0);
        $param['other_ext']                = Request::param('other_ext/s', '');
        $param['other_size']               = Request::param('other_size/s', 0);

        validate(SettingValidate::class)->scene($param['storage'])->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
