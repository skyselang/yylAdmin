<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

require_once '../extend/bce-php-sdk-0.9.18/BaiduBce.phar';

use app\common\service\file\SettingService;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use OSS\OssClient;
use OSS\Core\OssException;
use Qcloud\Cos\Client;
use BaiduBce\Services\Bos\BosClient;
use Upyun\Upyun;
use Upyun\Config;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\S3\Exception\S3MultipartUploadException;

/**
 * 对象存储
 */
class StorageService
{
    /**
     * 文件上传
     *
     * @param array $file_info 文件信息
     *
     * @return array
     */
    public static function upload($file_info, $pk = 'file_id')
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
        } elseif ($storage == 'upyun') {
            try {
                $serviceConfig = new Config($setting['upyun_service_name'], $setting['upyun_operator_name'], $setting['upyun_operator_pwd']);
                $client = new Upyun($serviceConfig);
                $path = $file_name;
                $content = fopen($file_path, "rb");
                $client->write($path, $content);
                $file_info['domain'] = $setting['upyun_domain'];
            } catch (\Exception $e) {
                $errmsg = $e->getMessage() ?: 'USS upload error';
            }
        } elseif ($storage == 'aws') {
            try {
                $credentials = new Credentials($setting['aws_access_key_id'], $setting['aws_secret_access_key']);
                //aws客户端
                $aws = new S3Client([
                    'version' => 'latest',
                    //AWS区域和终端节点： http://docs.amazonaws.cn/general/latest/gr/rande.html
                    'region' => $setting['aws_region'],
                    //加载证书
                    'credentials' => $credentials,
                    //bug调试
                    'debug' => config('app_debug')
                ]);

                //存储桶 获取AWS存储桶的名称
                $bucket = $setting['aws_bucket'];
                //需要上传的文件，文件的本地路径例:D:/www/abc.jpg;
                $source = $file_path;
                //多部件上传
                $uploader = new MultipartUploader($aws, $source, [
                    //存储桶
                    'bucket' => $bucket,
                    //上传后的新地址
                    'key'    => $file_name,
                    //设置访问权限 公开，不然访问不了
                    'ACL'    => 'public-read',
                    //分段上传
                    'before_initiate' => function (\Aws\Command $command) {
                        // $command is a CreateMultipartUpload operation
                        $command['CacheControl'] = 'max-age=3600';
                    },
                    'before_upload'   => function (\Aws\Command $command) {
                        // $command is an UploadPart operation
                        $command['RequestPayer'] = 'requester';
                    },
                    'before_complete' => function (\Aws\Command $command) {
                        // $command is a CompleteMultipartUpload operation
                        $command['RequestPayer'] = 'requester';
                    },
                ]);
            } catch (\Exception $e) {
                $errmsg = $e->getMessage() ?: 'aws upload error';
            }

            try {
                $result = $uploader->upload();
                //上传成功--返回上传后的地址
                urldecode($result['ObjectURL']);
            } catch (S3MultipartUploadException $e) {
                //上传失败--返回错误信息
                $uploader =  new MultipartUploader($aws, $source, [
                    'state' => $e->getState(),
                ]);
                $errmsg = $e->getMessage() ?: 'aws upload error';
            }
        }

        $file_info['storage'] = $storage;

        if ($errmsg) {
            if (empty($file_info[$pk])) {
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
