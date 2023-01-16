<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use app\common\cache\file\SettingCache;
use app\common\model\file\SettingModel;

/**
 * 文件设置
 */
class SettingService
{
    // 设置id
    private static $id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info($param = [], $field = '')
    {
        $id = self::$id;

        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['create_uid']  = $param['create_uid'] ?? 0;
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();

            $info['accept_ext'] = self::fileAccept($info);

            SettingCache::set($id, $info);
        }

        if ($field) {
            $res = [];
            $fields = explode(',', $field);
            foreach ($fields as $v) {
                if ($info[$v] ?? '') {
                    $res[$v] = $info[$v];
                }
            }
            return $res;
        }

        return $info;
    }

    /**
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @return array
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $id = self::$id;

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $info = $model->find($id);
        $res = $info->save($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($id);

        return $param;
    }

    /**
     * 文件类型
     * 
     * @param string $type 文件类型
     *
     * @return array|string 类型数组或描述
     */
    public static function fileTypes($type = '')
    {
        $types = [
            'image' => '图片',
            'video' => '视频',
            'audio' => '音频',
            'word'  => '文档',
            'other' => '其它'
        ];
        if ($type !== '') {
            return $types[$type] ?? '';
        }
        return $types;
    }

    /**
     * 文件储存方式
     * 
     * @param string $storage 储存方式
     *
     * @return array|string 储存方式数组或描述
     */
    public static function storages($storage = '')
    {
        $storages = [
            'local'   => '本地(服务器)',
            'qiniu'   => '七牛云 Kodo',
            'aliyun'  => '阿里云 OSS',
            'tencent' => '腾讯云 COS',
            'baidu'   => '百度云 BOS',
            'upyun'   => '又拍云 USS',
            'aws'     => 'AWS S3'
        ];
        if ($storage !== '') {
            return $storages[$storage] ?? '';
        }
        return $storages;
    }

    /**
     * 文件大小格式化
     *
     * @param int $file_size 文件大小（byte(B)字节）
     *
     * @return string
     */
    public static function fileSize($file_size = 0)
    {
        $p = 0;
        $format = 'B';
        if ($file_size > 0 && $file_size < 1024) {
            $p = 0;
            return number_format($file_size) . ' ' . $format;
        }
        if ($file_size >= 1024 && $file_size < pow(1024, 2)) {
            $p = 1;
            $format = 'KB';
        }
        if ($file_size >= pow(1024, 2) && $file_size < pow(1024, 3)) {
            $p = 2;
            $format = 'MB';
        }
        if ($file_size >= pow(1024, 3) && $file_size < pow(1024, 4)) {
            $p = 3;
            $format = 'GB';
        }
        if ($file_size >= pow(1024, 4) && $file_size < pow(1024, 5)) {
            $p = 3;
            $format = 'TB';
        }

        $file_size /= pow(1024, $p);

        return number_format($file_size, 2) . $format;
    }

    /**
     * 文件类型获取
     *
     * @param string $file_ext 文件后缀
     *
     * @return string image图片，video视频，audio音频，word文档，other其它
     */
    public static function fileType($file_ext = '')
    {
        $image_ext = [
            'jpg', 'png', 'jpeg', 'gif', 'bmp', 'webp', 'ico', 'svg', 'tif', 'pcx', 'tga', 'exif',
            'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'raw', 'wmf',  'avif', 'apng', 'xbm', 'fpx'
        ];
        $video_ext = [
            'mp4', 'avi', 'mkv', 'flv', 'rm', 'rmvb', 'webm', '3gp', 'mpeg', 'mpg', 'dat', 'asx', 'wmv',
            'mov', 'm4a', 'ogm', 'vob'
        ];
        $audio_ext = [
            'mp3', 'aac', 'wma', 'wav', 'ape', 'flac', 'ogg', 'adt', 'adts', 'cda', 'cd', 'wave',
            'aiff', 'midi', 'ra', 'rmx', 'vqf', 'amr'
        ];
        $word_ext = [
            'doc', 'docx', 'docm', 'dotx', 'dotm', 'txt',
            'xls', 'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam', 'csv',
            'ppt', 'pptx', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm', 'thmx'
        ];

        if (in_array($file_ext, $image_ext)) {
            return 'image';
        } elseif (in_array($file_ext, $video_ext)) {
            return 'video';
        } elseif (in_array($file_ext, $audio_ext)) {
            return 'audio';
        } elseif (in_array($file_ext, $word_ext)) {
            return 'word';
        } else {
            return 'other';
        }
    }

    /**
     * 文件链接
     *
     * @param array $file 文件信息
     *
     * @return string
     */
    public static function fileUrl($file)
    {
        $file_url = '';
        if ($file) {
            if ($file['storage'] == 'local') {
                $file_url = file_url($file['file_path']);
            } else {
                $file_url = $file['domain'] . '/' . $file['file_path'];
                if (strpos($file_url, 'http') !== 0) {
                    $file_url = 'http://' . $file_url;
                }
            }
        }

        return $file_url;
    }

    /**
     * 文件上传accept
     *
     * @param array $setting 文件设置信息
     *
     * @return string
     */
    public static function fileAccept($setting = [])
    {
        $accept = '';
        $file_type = ['image', 'video', 'audio', 'word', 'other'];
        foreach ($file_type as $vt) {
            if ($setting[$vt . '_ext'] ?? '') {
                $file_ext = explode(',', $setting[$vt . '_ext']);
                foreach ($file_ext as $ve) {
                    $accept .= '.' . $ve . ',';
                }
            }
        }

        return rtrim($accept, ',');
    }
}
