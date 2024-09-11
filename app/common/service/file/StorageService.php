<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

require_once '../extend/bce-php-sdk-0.9.23/index.php';
require_once '../extend/esdk-obs-php-3.23.11/obs-autoloader.php';

use think\facade\Log;
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
use Aws\S3\Exception\S3Exception;
use Obs\ObsClient;

/**
 * 对象存储
 */
class StorageService
{
    private static $pk = 'file_id';

    /**
     * 文件上传
     *
     * @param array $file_info 文件信息
     *
     * @return array|Exception
     */
    public static function upload($file_info)
    {
        $e = null;
        $exist = self::exist($file_info);
        $errmsg = '';
        $file_path = './' . $file_info['file_path'];
        $file_name = $file_info['file_path'];
        $setting = SettingService::info();
        $storage = $setting['storage'];
        if (!$exist) {
            if ($storage == 'qiniu') {
                $accessKey = $setting['qiniu_access_key'];
                $secretKey = $setting['qiniu_secret_key'];
                $bucket = $setting['qiniu_bucket'];
                try {
                    $auth = new Auth($accessKey, $secretKey); // 构建鉴权对象
                    $token = $auth->uploadToken($bucket); // 生成上传 Token
                    $filePath = $file_path; // 要上传文件的本地路径
                    $key = $file_name; // 上传到存储后保存的文件名
                    $uploadMgr = new UploadManager(); // 初始化 UploadManager 对象并进行文件的上传。
                    list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath, null, 'application/octet-stream', false, null, 'v2'); // 调用 UploadManager 的 putFile 方法进行文件的上传。
                    if ($err !== null) {
                        $errmsg = $err->message();
                    }
                } catch (\Exception $e) {
                }
            } elseif ($storage == 'aliyun') {
                $accessKeyId = $setting['aliyun_access_key_id'];
                $accessKeySecret = $setting['aliyun_access_key_secret'];
                $endpoint = $setting['aliyun_endpoint']; // Endpoint（地域节点）
                $bucket = $setting['aliyun_bucket']; // 设置存储空间名称。
                $object = $file_name; // 设置文件名称。
                $filePath = $file_path; // 由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
                try {
                    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                    $ossClient->uploadFile($bucket, $object, $filePath);
                } catch (OssException $e) {
                }
            } elseif ($storage == 'tencent') {
                // SECRETID和SECRETKEY请登录访问管理控制台进行查看和管理
                $secretId = $setting['tencent_secret_id']; //"云 API 密钥 SecretId";
                $secretKey = $setting['tencent_secret_key']; //"云 API 密钥 SecretKey";
                $region = $setting['tencent_region']; //设置一个默认的存储桶地域
                $cosClient = new Client([
                    'region' => $region,
                    'schema' => 'https', //协议头部，默认为http
                    'credentials' => ['secretId'  => $secretId, 'secretKey' => $secretKey]
                ]);
                // 上传文件
                try {
                    $bucket = $setting['tencent_bucket']; //存储桶名称 格式：BucketName-APPID
                    $key = $file_name; //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
                    $local_path = $file_path; //保存到用户本地路径
                    $body = fopen($local_path, "rb");
                    $cosClient->upload($bucket, $key, $body);
                } catch (\Exception $e) {
                }
            } elseif ($storage == 'baidu') {
                try {
                    // 设置BosClient的Access Key ID、Secret Access Key和ENDPOINT
                    $accessKeyId = $setting['baidu_access_key'];
                    $secretAccessKey = $setting['baidu_secret_key'];
                    $bos_config = [
                        'credentials' => ['accessKeyId' => $accessKeyId, 'secretAccessKey' => $secretAccessKey],
                        'endpoint' => $setting['baidu_endpoint'],
                    ];
                    $bucketName = $setting['baidu_bucket'];
                    $objectKey = $file_name;
                    $fileName = $file_path;
                    $client = new BosClient($bos_config);
                    $client->putObjectFromFile($bucketName, $objectKey, $fileName);
                } catch (\Exception $e) {
                }
            } elseif ($storage == 'upyun') {
                try {
                    $serviceName = $setting['upyun_service_name'];
                    $operatorName = $setting['upyun_operator_name'];
                    $operatorPassword = $setting['upyun_operator_pwd'];
                    $serviceConfig = new Config($serviceName, $operatorName, $operatorPassword);
                    $client = new Upyun($serviceConfig);
                    $path = $file_name;
                    $content = fopen($file_path, "rb");
                    $client->write($path, $content);
                } catch (\Exception $e) {
                }
            } elseif ($storage == 'huawei') {
                try {
                    $obsClient = new ObsClient([
                        'key' => $setting['huawei_access_key_id'],
                        'secret' => $setting['huawei_secret_access_key'],
                        'endpoint' => $setting['huawei_endpoint']
                    ]);

                    $obsClient->putObject([
                        'Bucket' => $setting['huawei_bucket'],
                        'Key' => $file_name,
                        'SourceFile' => $file_path,
                    ]);
                } catch (\Exception $e) {
                }
            } elseif ($storage == 'aws') {
                try {
                    $credentials = new Credentials($setting['aws_access_key_id'], $setting['aws_secret_access_key']);
                    $aws = new S3Client([ //aws客户端
                        'version' => 'latest',
                        'region' => $setting['aws_region'], //AWS区域和终端节点
                        'credentials' => $credentials, //加载证书
                    ]);
                    $bucket = $setting['aws_bucket']; //存储桶 获取AWS存储桶的名称
                    $source = $file_path; //需要上传的文件，文件的本地路径例:D:/www/abc.jpg;
                    $uploader = new MultipartUploader($aws, $source, [ //多部件上传
                        'bucket' => $bucket, //存储桶
                        'key'    => $file_name, //上传后的新地址
                        'ACL'    => 'public-read', //设置访问权限 公开，不然访问不了
                        'before_initiate' => function (\Aws\Command $command) { //分段上传
                            $command['CacheControl'] = 'max-age=3600';
                        },
                        'before_upload'   => function (\Aws\Command $command) {
                            $command['RequestPayer'] = 'requester';
                        },
                        'before_complete' => function (\Aws\Command $command) {
                            $command['RequestPayer'] = 'requester';
                        },
                    ]);
                    $uploader->upload();
                } catch (\Exception $e) {
                }
            }
        }

        $file_info = self::domain($file_info);
        $file_info['storage'] = $storage;

        if ($e) {
            $errmsg = $e->getMessage() ?? $storage . ' upload error';
            self::log($e, $storage);
        }
        if ($errmsg) {
            if (empty($file_info[self::$pk])) {
                try {
                    unlink($file_path);
                } catch (\Exception $e) {
                }
            }
            exception($errmsg);
        } else {
            if ($storage != 'local') {
                try {
                    unlink($file_path);
                } catch (\Exception $e) {
                }
            }
        }

        return $file_info;
    }

    /**
     * 文件是否存在
     *
     * @param  array $file_info
     * @return bool
     */
    public static function exist($file_info)
    {
        $e = null;
        $file_path = $file_info['file_path'];
        $setting = SettingService::info();
        $storage = $setting['storage'];
        if ($storage == 'qiniu') {
            try {
                $accessKey = $setting['qiniu_access_key'];
                $secretKey = $setting['qiniu_secret_key'];
                $bucket = $setting['qiniu_bucket'];
                $key = $file_path;
                $auth = new Auth($accessKey, $secretKey);
                $config = new \Qiniu\Config();
                $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
                list($fileInfo) = $bucketManager->stat($bucket, $key);
                if ($fileInfo) {
                    return true;
                }
            } catch (\Exception $e) {
            }
        } elseif ($storage == 'aliyun') {
            try {
                $accessKeyId = $setting['aliyun_access_key_id'];
                $accessKeySecret = $setting['aliyun_access_key_secret'];
                $endpoint = $setting['aliyun_endpoint'];
                $bucket = $setting['aliyun_bucket'];
                $object = $file_path;
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                return $ossClient->doesObjectExist($bucket, $object);
            } catch (OssException $e) {
            }
        } elseif ($storage == 'tencent') {
            try {
                $secretId = $setting['tencent_secret_id'];
                $secretKey = $setting['tencent_secret_key'];
                $region = $setting['tencent_region'];
                $bucket = $setting['tencent_bucket'];
                $object = $file_path;
                $cosClient = new Client([
                    'region' => $region,
                    'scheme' => 'https',
                    'credentials' => ['secretId' => $secretId, 'secretKey' => $secretKey]
                ]);
                return $cosClient->doesObjectExist($bucket, $object);
            } catch (\Exception $e) {
            }
        } elseif ($storage == 'baidu') {
            try {
                $bos_config = [
                    'credentials' => [
                        'accessKeyId' => $setting['baidu_access_key'],
                        'secretAccessKey' => $setting['baidu_secret_key']
                    ],
                    'endpoint' => $setting['baidu_endpoint'],
                ];
                $bucket = $setting['baidu_bucket'];
                $object = $file_path;
                $client = new BosClient($bos_config);
                $client->getObjectMetadata($bucket, $object);
                return true;
            } catch (\BaiduBce\Exception\BceServiceException $e) {
                if (strpos($e->getMessage(), 'status:404')) {
                    return false;
                }
            }
        } elseif ($storage == 'upyun') {
            try {
                $serviceName = $setting['upyun_service_name'];
                $operatorName = $setting['upyun_operator_name'];
                $operatorPassword = $setting['upyun_operator_pwd'];
                $serviceConfig = new Config($serviceName, $operatorName, $operatorPassword);
                $client = new Upyun($serviceConfig);
                $path = $file_path;
                return $client->has($path);
            } catch (\Exception $e) {
            }
        } elseif ($storage == 'huawei') {
            try {
                $obsClient = new ObsClient([
                    'key' => $setting['huawei_access_key_id'],
                    'secret' => $setting['huawei_secret_access_key'],
                    'endpoint' => $setting['huawei_endpoint']
                ]);
                $obsClient->getObjectMetadata([
                    'Bucket' => $setting['huawei_bucket'],
                    'Key' => $file_path,
                ]);
                return true;
            } catch (\Exception $e) {
            }
        } elseif ($storage == 'aws') {
            try {
                $credentials = new Credentials($setting['aws_access_key_id'], $setting['aws_secret_access_key']);
                $aws = new S3Client([
                    'version' => 'latest',
                    'region' => $setting['aws_region'],
                    'credentials' => $credentials,
                ]);
                $bucket = $setting['aws_bucket'];
                $key = $file_path;
                $aws->headObject(['Bucket' => $bucket, 'Key' => $key]);
                return true;
            } catch (S3Exception $e) {
            }
        }

        if ($e) {
            self::log($e, $storage);
        }

        return false;
    }

    /**
     * 文件删除
     *
     * @param  array $filelist
     * @return void
     */
    public static function dele($filelist)
    {
        $qinius = $aliyuns = $tencents = $baidus = $upyuns = $awss = $huaweis = [];
        foreach ($filelist as $file) {
            if ($file['storage'] == 'qiniu') {
                $qinius[] = $file['file_path'];
            } elseif ($file['storage'] == 'aliyun') {
                $aliyuns[] = $file['file_path'];
            } elseif ($file['storage'] == 'tencent') {
                $tencents[] = ['Key' => $file['file_path']];
            } elseif ($file['storage'] == 'baidu') {
                $baidus[] = ['key' => $file['file_path']];
            } elseif ($file['storage'] == 'upyun') {
                $upyuns[] = $file;
            } elseif ($file['storage'] == 'huawei') {
                $huaweis[] = ['Key' => $file['file_path']];
            } elseif ($file['storage'] == 'aws') {
                $awss[] = ['Key' => $file['file_path']];
            }
        }

        $setting = SettingService::info();

        if ($qinius) {
            try {
                $accessKey = $setting['qiniu_access_key'];
                $secretKey = $setting['qiniu_secret_key'];
                $bucket = $setting['qiniu_bucket'];
                $keys = $qinius;
                $auth = new Auth($accessKey, $secretKey);
                $config = new \Qiniu\Config();
                $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
                $ops = $bucketManager->buildBatchDelete($bucket, $keys);
                $bucketManager->batch($ops);
            } catch (\Exception $e) {
                self::log($e, 'qiniu');
            }
        }

        if ($aliyuns) {
            try {
                $accessKeyId = $setting['aliyun_access_key_id'];
                $accessKeySecret = $setting['aliyun_access_key_secret'];
                $endpoint = $setting['aliyun_endpoint'];
                $bucket = $setting['aliyun_bucket'];
                $objects = $aliyuns;
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                $ossClient->deleteObjects($bucket, $objects);
            } catch (\Exception $e) {
                self::log($e, 'aliyun');
            }
        }

        if ($tencents) {
            try {
                $secretId = $setting['tencent_secret_id'];
                $secretKey = $setting['tencent_secret_key'];
                $region = $setting['tencent_region'];
                $bucket = $setting['tencent_bucket'];
                $objects = $tencents;
                $cosClient = new Client([
                    'region' => $region,
                    'scheme' => 'https',
                    'credentials' => ['secretId' => $secretId, 'secretKey' => $secretKey]
                ]);
                $cosClient->deleteObjects(['Bucket' => $bucket, 'Objects' => $objects]);
            } catch (\Exception $e) {
                self::log($e, 'tencent');
            }
        }

        if ($baidus) {
            try {
                $bos_config = [
                    'credentials' => [
                        'accessKeyId' => $setting['baidu_access_key'],
                        'secretAccessKey' => $setting['baidu_secret_key']
                    ],
                    'endpoint' => $setting['baidu_endpoint'],
                ];
                $bucket = $setting['baidu_bucket'];
                $client = new BosClient($bos_config);
                $deleteattay = $baidus;
                $client->deleteMultipleObjects($bucket, $deleteattay);
            } catch (\Exception $e) {
                self::log($e, 'baidu');
            }
        }

        if ($upyuns) {
            try {
                $serviceName = $setting['upyun_service_name'];
                $operatorName = $setting['upyun_operator_name'];
                $operatorPassword = $setting['upyun_operator_pwd'];
                $serviceConfig = new Config($serviceName, $operatorName, $operatorPassword);
                $client = new Upyun($serviceConfig);
                foreach ($upyuns as $upy) {
                    $client->delete($upy['file_path'], true);
                }
            } catch (\Exception $e) {
                self::log($e, 'upyun');
            }
        }

        if ($huaweis) {
            try {
                $obsClient = new ObsClient([
                    'key' => $setting['huawei_access_key_id'],
                    'secret' => $setting['huawei_secret_access_key'],
                    'endpoint' => $setting['huawei_endpoint']
                ]);

                $obsClient->deleteObjects([
                    'Bucket' => $setting['huawei_bucket'],
                    'Quiet' => true,
                    'Objects' => $huaweis,
                ]);
            } catch (\Exception $e) {
                self::log($e, 'huawei');
            }
        }

        if ($awss) {
            try {
                $credentials = new Credentials($setting['aws_access_key_id'], $setting['aws_secret_access_key']);
                $aws = new S3Client([
                    'version' => 'latest',
                    'region' => $setting['aws_region'],
                    'credentials' => $credentials,
                ]);
                $bucket = $setting['aws_bucket'];
                $objects = $awss;
                $aws->deleteObjects(['Bucket' => $bucket, 'Delete' => ['Objects' => $objects]]);
            } catch (\Exception $e) {
                self::log($e, 'aws');
            }
        }
    }

    /**
     * 文件域名
     *
     * @param  array $file_info
     * @return array
     */
    public static function domain($file_info)
    {
        $setting = SettingService::info();
        $storage = $setting['storage'];
        $file_info['domain'] = $setting[$storage . '_domain'] ?? '';
        return $file_info;
    }

    /**
     * 文件日志
     *
     * @param  \Exception $e
     * @param  string $storage
     * @return void
     */
    public static function log($e, $storage)
    {
        $log = [
            'storage' => $storage,
            'code'    => $e->getCode(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
            'message' => $e->getMessage(),
            'trace0'  => $e->getTrace()[0] ?? [],
        ];
        Log::write($log, 'oss');
    }
}
