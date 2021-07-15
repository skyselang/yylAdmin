<?php
/*
 * @Description  : 文件上传
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-07-14
 * @LastEditTime : 2021-07-15
 */

namespace app\common\service;

use think\facade\Filesystem;
use app\common\utils\ByteUtils;

class UploadService
{
    /**
     * 上传文件
     *
     * @param file   $param   文件信息
     * @param string $savedir 保存目录
     * @param string $param   类型file、image、video
     *
     * @return array
     */
    public static function upload($file, $savedir = 'upload', $type = 'image')
    {
        $file_name = Filesystem::disk('public')
            ->putFile($savedir, $file, function () use ($type) {
                return date('Ymd') . '/' . date('YmdHis') . '_' . $type;
            });

        $data['type'] = $type;
        $data['path'] = 'storage/' . $file_name;
        $data['url']  = file_url($data['path']);
        $data['name'] = $file->getOriginalName();
        $data['size'] = ByteUtils::format($file->getSize());

        return $data;
    }
}
