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

/**
 * 文件设置验证器
 */
class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'storage|存储方式'                              => ['require'],
        'qiniu_access_key|AccessKey'                => ['require'],
        'qiniu_secret_key|SecretKey'                => ['require'],
        'qiniu_bucket|空间名称'                         => ['require'],
        'qiniu_domain|外链域名'                         => ['require'],
        'aliyun_access_key_id|AccessKey ID'         => ['require'],
        'aliyun_access_key_secret|AccessKey Secret' => ['require'],
        'aliyun_bucket|Bucket名称'                    => ['require'],
        'aliyun_bucket_domain|Bucket域名'             => ['require'],
        'aliyun_endpoint|Endpoint（地域节点）'            => ['require'],
        'tencent_secret_id|SecretId'                => ['require'],
        'tencent_secret_key|SecretKey'              => ['require'],
        'tencent_bucket|存储桶名称'                      => ['require'],
        'tencent_region|所属地域'                       => ['require'],
        'tencent_domain|访问域名'                       => ['require'],
        'baidu_access_key|Access Key'               => ['require'],
        'baidu_secret_key|Secret Key'               => ['require'],
        'baidu_bucket|Bucket名称'                     => ['require'],
        'baidu_domain|官方域名'                         => ['require'],
        'baidu_endpoint|所属地域'                       => ['require'],
        'upyun_service_name|服务名称'                   => ['require'],
        'upyun_operator_name|操作员'                   => ['require'],
        'upyun_operator_pwd|操作员密码'                  => ['require'],
        'upyun_domain|加速域名'                         => ['require'],
        's3_access_key_id|Access Key ID'            => ['require'],
        's3_secret_access_key|Secret Access KEY'    => ['require'],
        's3_region|区域终端节点'                          => ['require'],
        's3_bucket|存储桶名称'                           => ['require'],
        's3_domain|访问域名'                            => ['require'],
        'image_size|图片大小'                           => ['require', '>=:0', 'float'],
        'video_size|视频大小'                           => ['require', '>=:0', 'float'],
        'audio_size|音频大小'                           => ['require', '>=:0', 'float'],
        'word_size|文档大小'                            => ['require', '>=:0', 'float'],
        'other_size|其它大小'                           => ['require', '>=:0', 'float'],
        'limit_max|最大上传个数'                          => ['require', '>:0', 'number'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'local'   => ['storage', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
        'qiniu'   => ['storage', 'qiniu_access_key', 'qiniu_secret_key', 'qiniu_bucket', 'qiniu_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
        'aliyun'  => ['storage', 'aliyun_access_key_id', 'aliyun_access_key_secret', 'aliyun_bucket', 'aliyun_endpoint',  'aliyun_bucket_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
        'tencent' => ['storage', 'tencent_secret_id', 'tencent_secret_key', 'tencent_bucket', 'tencent_region', 'tencent_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
        'baidu'   => ['storage', 'baidu_access_key', 'baidu_secret_key', 'baidu_bucket', 'baidu_endpoint', 'baidu_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
        'upyun'   => ['storage', 'upyun_service_name', 'upyun_operator_name', 'upyun_operator_pwd', 'upyun_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
        's3'      => ['storage', 's3_access_key_id', 's3_secret_access_key', 's3_bucket', 's3_region', 's3_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size', 'limit_max'],
    ];
}
