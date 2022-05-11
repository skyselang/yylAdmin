<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件设置验证器
namespace app\common\validate\file;

use think\Validate;

class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'setting_id'               => ['require'],
        'storage'                  => ['require'],
        'qiniu_access_key'         => ['require'],
        'qiniu_secret_key'         => ['require'],
        'qiniu_bucket'             => ['require'],
        'qiniu_domain'             => ['require'],
        'aliyun_access_key_id'     => ['require'],
        'aliyun_access_key_secret' => ['require'],
        'aliyun_bucket'            => ['require'],
        'aliyun_endpoint'          => ['require'],
        'aliyun_bucket_domain'     => ['require'],
        'tencent_secret_id'        => ['require'],
        'tencent_secret_key'       => ['require'],
        'tencent_bucket'           => ['require'],
        'tencent_region'           => ['require'],
        'tencent_domain'           => ['require'],
        'baidu_access_key'         => ['require'],
        'baidu_secret_key'         => ['require'],
        'baidu_bucket'             => ['require'],
        'baidu_endpoint'           => ['require'],
        'baidu_domain'             => ['require'],
        'image_size'               => ['checkFileSize'],
        'video_size'               => ['checkFileSize'],
        'audio_size'               => ['checkFileSize'],
        'word_size'                => ['checkFileSize'],
        'other_size'               => ['checkFileSize'],
    ];

    // 错误信息
    protected $message = [
        'qiniu_access_key.require'         => '请输入 AccessKey',
        'qiniu_secret_key.require'         => '请输入 SecretKey',
        'qiniu_bucket.require'             => '请输入空间名称',
        'qiniu_domain.require'             => '请输入外链域名',
        'aliyun_access_key_id.require'     => '请输入 AccessKey ID',
        'aliyun_access_key_secret.require' => '请输入 AccessKey Secret',
        'aliyun_endpoint.require'          => '请输入 Endpoint（地域节点）',
        'aliyun_bucket.require'            => '请输入 Bucket 名称',
        'tencent_secret_id.require'        => '请输入 SecretId',
        'tencent_secret_key.require'       => '请输入 SecretKey',
        'tencent_bucket.require'           => '请输入存储桶名称',
        'tencent_region.require'           => '请输入所属地域',
        'tencent_domain.require'           => '请输入访问域名',
        'baidu_access_key.require'         => '请输入 AccessKey',
        'baidu_secret_key.require'         => '请输入 SecretKey',
        'baidu_bucket.require'             => '请输入 Bucket 名称',
        'baidu_endpoint.require'           => '请输入所属地域',
        'baidu_domain.require'             => '请输入官方域名',
    ];

    // 验证场景
    protected $scene = [
        'local'   => ['storage', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size'],
        'qiniu'   => ['storage', 'qiniu_access_key', 'qiniu_secret_key', 'qiniu_bucket', 'qiniu_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size'],
        'aliyun'  => ['storage', 'aliyun_access_key_id', 'aliyun_access_key_secret', 'aliyun_bucket', 'aliyun_endpoint',  'aliyun_bucket_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size'],
        'tencent' => ['storage', 'tencent_secret_id', 'tencent_secret_key', 'tencent_bucket', 'tencent_region', 'tencent_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size'],
        'baidu'   => ['storage', 'baidu_access_key', 'baidu_secret_key', 'baidu_bucket', 'baidu_endpoint', 'baidu_domain', 'image_size', 'video_size', 'audio_size', 'word_size', 'other_size'],
    ];

    // 自定义验证规则：文件大小
    protected function checkFileSize($value, $rule, $data = [])
    {
        $type = ['image' => '图片', 'video' => '视频', 'audio' => '音频', 'word' => '文档', 'other' => '其它'];
        $message = '大小：只能为数字且不能为负数';
        foreach ($type as $k => $v) {
            if (!is_numeric($data[$k . '_size'])) {
                return $v . $message;
            }
            if ($data[$k . '_size'] < 0) {
                return $v . $message;
            }
        }

        return true;
    }
}
