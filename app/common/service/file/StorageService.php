<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 对象存储
namespace app\common\service\file;

require_once '../extend/bce-php-sdk-0.9.16/BaiduBce.phar';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use OSS\OssClient;
use OSS\Core\OssException;
use Qcloud\Cos\Client;
use BaiduBce\Services\Bos\BosClient;

class StorageService
{
    /**
     * 文件上传
     *
     * @param array $file_info 文件信息
     *
     * @return array
     */
    public static function upload($file_info)
    {
        $errmsg = '';
        $file_info['domain'] = '';
        $file_path = './' . $file_info['file_path'];
        $file_name = $file_info['file_path'];
        $setting = SettingService::info();
        $storage = $setting['storage'];
        if ($storage == 'qiniu') {
            $accessKey = $setting['qiniu_access_key'];
            $secretKey = $setting['qiniu_secret_key'];
            $bucket = $setting['qiniu_bucket'];
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = $file_path;
            // 上传到存储后保存的文件名
            $key = $file_name;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                $errmsg = isset($err['error']) ? $err['error'] : 'Kodo upload error';
            }
            $file_info['domain'] = $setting['qiniu_domain'];
        } elseif ($storage == 'aliyun') {
            $accessKeyId = $setting['aliyun_access_key_id'];
            $accessKeySecret = $setting['aliyun_access_key_secret'];
            // Endpoint（地域节点）
            $endpoint = $setting['aliyun_endpoint'];
            // 设置存储空间名称。
            $bucket = $setting['aliyun_bucket'];
            // 设置文件名称。
            $object = $file_name;
            // 由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
            $filePath = $file_path;
            try {
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                $ossClient->uploadFile($bucket, $object, $filePath);
            } catch (OssException $e) {
                $errmsg = $e->getMessage() ?: 'OSS upload error';
            }
            $file_info['domain'] = $setting['aliyun_bucket_domain'];
        } elseif ($storage == 'tencent') {
            // SECRETID和SECRETKEY请登录访问管理控制台进行查看和管理
            $secretId = $setting['tencent_secret_id']; //"云 API 密钥 SecretId";
            $secretKey = $setting['tencent_secret_key']; //"云 API 密钥 SecretKey";
            $region =  $setting['tencent_region']; //设置一个默认的存储桶地域
            $cosClient = new Client(
                [
                    'region' => $region,
                    'schema' => 'https', //协议头部，默认为http
                    'credentials' => [
                        'secretId'  => $secretId,
                        'secretKey' => $secretKey
                    ]
                ]
            );
            // 上传文件
            try {
                $bucket = $setting['tencent_bucket']; //存储桶名称 格式：BucketName-APPID
                $key = $file_name; //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
                $srcPath = $file_path; //本地文件绝对路径
                $file = fopen($srcPath, "rb");
                if ($file) {
                    $cosClient->putObject([
                        'Bucket' => $bucket,
                        'Key' => $key,
                        'Body' => $file
                    ]);
                }
            } catch (\Exception $e) {
                $errmsg = $e->getMessage() ?: 'COS upload error';
            }
            $file_info['domain'] = $setting['tencent_domain'];
        } elseif ($storage == 'baidu') {
            try {
                // 设置BosClient的Access Key ID、Secret Access Key和ENDPOINT
                $bos_config = [
                    'credentials' => [
                        'accessKeyId' => $setting['baidu_access_key'],
                        'secretAccessKey' => $setting['baidu_secret_key']
                    ],
                    'endpoint' => $setting['baidu_endpoint'],
                ];
                $bucketName = $setting['baidu_bucket'];
                $objectKey = $file_name;
                $fileName = $file_path;
                $client = new BosClient($bos_config);
                $client->putObjectFromFile($bucketName, $objectKey, $fileName);
                $file_info['domain'] = $setting['baidu_domain'];
            } catch (\Exception $e) {
                $errmsg = $e->getMessage() ?: 'BOS upload error';
            }
        }
        $file_info['storage'] = $storage;

        if ($errmsg) {
            if (empty($file_info['file_id'])) {
                @unlink($file_path);
            }
            exception($errmsg);
        } else {
            if ($storage != 'local') {
                @unlink($file_path);
            }
        }

        return $file_info;
    }
}
