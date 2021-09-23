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
        'local'   => ['storage'],
        'qiniu'   => ['storage', 'qiniu_access_key', 'qiniu_secret_key', 'qiniu_bucket', 'qiniu_domain'],
        'aliyun'  => ['storage', 'aliyun_access_key_id', 'aliyun_access_key_secret', 'aliyun_bucket', 'aliyun_endpoint',  'aliyun_bucket_domain'],
        'tencent' => ['storage', 'tencent_secret_id', 'tencent_secret_key', 'tencent_bucket', 'tencent_region', 'tencent_domain'],
        'baidu'   => ['storage', 'baidu_access_key', 'baidu_secret_key', 'baidu_bucket', 'baidu_endpoint', 'baidu_domain'],
    ];
}
