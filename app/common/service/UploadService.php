<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件上传
namespace app\common\service;

use think\UploadedFile;
use think\facade\Filesystem;
use app\common\utils\ByteUtils;

class UploadService
{
    /**
     * 上传文件
     *
     * @param UploadedFile File $file 文件
     * @param string $path 保存路径
     *
     * @return array
     */
    public static function upload($file, $path = 'upload')
    {
        $file_name = Filesystem::disk('public')
            ->putFile($path, $file, function () use ($file) {
                return date('Ymd') . '/' . $file->hash('sha1');
            });

        $data['path'] = 'storage/' . $file_name;
        $data['url']  = file_url($data['path']);
        $data['name'] = $file->getOriginalName();
        $data['size'] = ByteUtils::format($file->getSize());

        return $data;
    }
}
