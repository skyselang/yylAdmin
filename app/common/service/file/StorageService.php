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

use app\common\model\file\FileModel;
use app\common\service\file\SettingService;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use OSS\OssClient;
use OSS\Core\OssException;
use Qcloud\Cos\Client;
use BaiduBce\Services\Bos\BosClient;
use Upyun\Upyun;
use Upyun\Config;
use Obs\ObsClient;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * 对象存储
 */
class StorageService
{
    /**
     * 文件模型
     */
    public static function model()
    {
        return new FileModel();
    }

    /**
     * 文件主键
     */
    public static function pk()
    {
        $model = self::model();
        return $model->getPk();
    }

    /**
     * 文件上传
     * @param array $file_info 文件信息
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
            if ($storage === 'qiniu') {
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
            } elseif ($storage === 'aliyun') {
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
            } elseif ($storage === 'tencent') {
                // SECRETID和SECRETKEY请登录访问管理控制台进行查看和管理
                $secretId = $setting['tencent_secret_id']; //"云 API 密钥 SecretId";
                $secretKey = $setting['tencent_secret_key']; //"云 API 密钥 SecretKey";
                $region = $setting['tencent_region']; //设置一个默认的存储桶地域
                $CosClient = new Client([
                    'region' => $region,
                    'schema' => 'https', //协议头部，默认为http
                    'credentials' => ['secretId'  => $secretId, 'secretKey' => $secretKey]
                ]);
                // 上传文件
                try {
                    $bucket = $setting['tencent_bucket']; //存储桶名称 格式：BucketName-APPID
                    $key = $file_name; //此处的 key 为对象键，对象键是对象在存储桶中的唯一编号
                    $local_path = $file_path; //保存到用户本地路径
                    $body = fopen($local_path, "rb");
                    $CosClient->upload($bucket, $key, $body);
                } catch (\Exception $e) {
                }
            } elseif ($storage === 'baidu') {
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
                    $BosClient = new BosClient($bos_config);
                    $BosClient->putObjectFromFile($bucketName, $objectKey, $fileName);
                } catch (\Exception $e) {
                }
            } elseif ($storage === 'upyun') {
                try {
                    $serviceName = $setting['upyun_service_name'];
                    $operatorName = $setting['upyun_operator_name'];
                    $operatorPassword = $setting['upyun_operator_pwd'];
                    $path = $file_name;
                    $content = fopen($file_path, "rb");
                    $serviceConfig = new Config($serviceName, $operatorName, $operatorPassword);
                    $Upyun = new Upyun($serviceConfig);
                    $Upyun->write($path, $content);
                } catch (\Exception $e) {
                }
            } elseif ($storage === 'huawei') {
                try {
                    $ObsClient = new ObsClient([
                        'key' => $setting['huawei_access_key_id'],
                        'secret' => $setting['huawei_secret_access_key'],
                        'endpoint' => $setting['huawei_endpoint']
                    ]);
                    $ObsClient->putObject([
                        'Bucket' => $setting['huawei_bucket'],
                        'Key' => $file_name,
                        'SourceFile' => $file_path,
                    ]);
                } catch (\Exception $e) {
                }
            } elseif ($storage === 'aws') {
                try {
                    $credentials = new Credentials($setting['aws_access_key_id'], $setting['aws_secret_access_key']);
                    $S3Client  = new S3Client([
                        'region' => $setting['aws_region'],
                        'endpoint' => $setting['aws_endpoint'],
                        'credentials' => $credentials,
                    ]);
                    $S3Client->putObject([
                        'Bucket' => $setting['aws_bucket'],
                        'Key' => $file_name,
                        'SourceFile' => $file_path,
                    ]);
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
            if (empty($file_info[self::pk()])) {
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
     * @param array $file_info
     */
    public static function exist($file_info)
    {
        $e = null;
        $file_path = $file_info['file_path'];
        $setting = SettingService::info();
        $storage = $setting['storage'];
        if ($storage === 'qiniu') {
            try {
                $auth = new Auth($setting['qiniu_access_key'], $setting['qiniu_secret_key']);
                $config = new \Qiniu\Config();
                $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
                list($fileInfo) = $bucketManager->stat($setting['qiniu_bucket'], $file_path);
                if ($fileInfo) {
                    return true;
                }
            } catch (\Exception $e) {
            }
        } elseif ($storage === 'aliyun') {
            try {
                $accessKeyId = $setting['aliyun_access_key_id'];
                $accessKeySecret = $setting['aliyun_access_key_secret'];
                $endpoint = $setting['aliyun_endpoint'];
                $OssClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                return $OssClient->doesObjectExist($setting['aliyun_bucket'], $file_path);
            } catch (OssException $e) {
            }
        } elseif ($storage === 'tencent') {
            try {
                $CosClient = new Client([
                    'region' => $setting['tencent_region'],
                    'scheme' => 'https',
                    'credentials' => [
                        'secretId' => $setting['tencent_secret_id'],
                        'secretKey' => $setting['tencent_secret_key']
                    ]
                ]);
                return $CosClient->doesObjectExist($setting['tencent_bucket'], $file_path);
            } catch (\Exception $e) {
            }
        } elseif ($storage === 'baidu') {
            try {
                $BosClient = new BosClient([
                    'credentials' => [
                        'accessKeyId' => $setting['baidu_access_key'],
                        'secretAccessKey' => $setting['baidu_secret_key']
                    ],
                    'endpoint' => $setting['baidu_endpoint'],
                ]);
                $BosClient->getObjectMetadata($setting['baidu_bucket'], $file_path);
                return true;
            } catch (\BaiduBce\Exception\BceServiceException $e) {
                if (strpos($e->getMessage(), 'status:404')) {
                    return false;
                }
            }
        } elseif ($storage === 'upyun') {
            try {
                $serviceName = $setting['upyun_service_name'];
                $operatorName = $setting['upyun_operator_name'];
                $operatorPassword = $setting['upyun_operator_pwd'];
                $serviceConfig = new Config($serviceName, $operatorName, $operatorPassword);
                $Upyun = new Upyun($serviceConfig);
                return $Upyun->has($file_path);
            } catch (\Exception $e) {
            }
        } elseif ($storage === 'huawei') {
            try {
                $ObsClient = new ObsClient([
                    'key' => $setting['huawei_access_key_id'],
                    'secret' => $setting['huawei_secret_access_key'],
                    'endpoint' => $setting['huawei_endpoint']
                ]);
                $ObsClient->getObjectMetadata(['Bucket' => $setting['huawei_bucket'], 'Key' => $file_path]);
                return true;
            } catch (\Exception $e) {
            }
        } elseif ($storage === 'aws') {
            try {
                $credentials = new Credentials($setting['aws_access_key_id'], $setting['aws_secret_access_key']);
                $S3Client = new S3Client([
                    'region' => $setting['aws_region'],
                    'endpoint' => $setting['aws_endpoint'],
                    'credentials' => $credentials,
                ]);
                $S3Client->headObject(['Bucket' => $setting['aws_bucket'], 'Key' => $file_path]);
                return true;
            } catch (S3Exception $e) {
            }
        }

        if ($e && $e->getCode() != 404) {
            if (!str_contains($e->getMessage(), '404')) {
                self::log($e, $storage);
            }
        }

        return false;
    }

    /**
     * 文件删除
     * @param array $filelist
     */
    public static function dele($filelist)
    {
        $qinius = $aliyuns = $tencents = $baidus = $upyuns = $awss = $huaweis = [];
        foreach ($filelist as $file) {
            if ($file['storage'] === 'qiniu') {
                $qinius[] = $file['file_path'];
            } elseif ($file['storage'] === 'aliyun') {
                $aliyuns[] = $file['file_path'];
            } elseif ($file['storage'] === 'tencent') {
                $tencents[] = ['Key' => $file['file_path']];
            } elseif ($file['storage'] === 'baidu') {
                $baidus[] = ['key' => $file['file_path']];
            } elseif ($file['storage'] === 'upyun') {
                $upyuns[] = $file;
            } elseif ($file['storage'] === 'huawei') {
                $huaweis[] = ['Key' => $file['file_path']];
            } elseif ($file['storage'] === 'aws') {
                $awss[] = ['Key' => $file['file_path']];
            }
        }

        $setting = SettingService::info();

        if ($qinius) {
            try {;
                $auth = new Auth($setting['qiniu_access_key'], $setting['qiniu_secret_key']);
                $config = new \Qiniu\Config();
                $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
                $ops = $bucketManager->buildBatchDelete($setting['qiniu_bucket'], $qinius);
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
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                $ossClient->deleteObjects($setting['aliyun_bucket'], $aliyuns);
            } catch (\Exception $e) {
                self::log($e, 'aliyun');
            }
        }

        if ($tencents) {
            try {
                $cosClient = new Client([
                    'region' => $setting['tencent_region'],
                    'scheme' => 'https',
                    'credentials' => [
                        'secretId' => $setting['tencent_secret_id'],
                        'secretKey' => $setting['tencent_secret_key']
                    ]
                ]);
                $cosClient->deleteObjects(['Bucket' => $setting['tencent_bucket'], 'Objects' => $tencents]);
            } catch (\Exception $e) {
                self::log($e, 'tencent');
            }
        }

        if ($baidus) {
            try {
                $BosClient = new BosClient([
                    'credentials' => [
                        'accessKeyId' => $setting['baidu_access_key'],
                        'secretAccessKey' => $setting['baidu_secret_key']
                    ],
                    'endpoint' => $setting['baidu_endpoint'],
                ]);
                $BosClient->deleteMultipleObjects($setting['baidu_bucket'], $baidus);
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
                $Upyun = new Upyun($serviceConfig);
                foreach ($upyuns as $upy) {
                    $Upyun->delete($upy['file_path'], true);
                }
            } catch (\Exception $e) {
                self::log($e, 'upyun');
            }
        }

        if ($huaweis) {
            try {
                $ObsClient = new ObsClient([
                    'key' => $setting['huawei_access_key_id'],
                    'secret' => $setting['huawei_secret_access_key'],
                    'endpoint' => $setting['huawei_endpoint']
                ]);
                $ObsClient->deleteObjects([
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
                $S3Client = new S3Client([
                    'region' => $setting['aws_region'],
                    'endpoint' => $setting['aws_endpoint'],
                    'credentials' => $credentials,
                ]);
                foreach ($awss as $aws) {
                    $S3Client->deleteObject(['Bucket' => $setting['aws_bucket'], 'Key' => $aws['Key']]);
                }
            } catch (\Exception $e) {
                self::log($e, 'aws');
            }
        }
    }

    /**
     * 文件域名
     * @param array $file_info
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
     * @param \Exception $e
     * @param string     $storage
     */
    public static function log($e, $storage)
    {
        $data = [
            'storage' => $storage,
            'code'    => $e->getCode(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
            'message' => $e->getMessage(),
        ];
        $log['type'] = 'oss';
        $log['data'] = $data;
        trace($log, 'log');
    }
}
