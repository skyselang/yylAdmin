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
use hg\apidoc\annotation as Apidoc;

/**
 * 文件设置
 */
class SettingService
{
    /**
     * 设置id
     * @var integer
     */
    private static $id = 1;

    /**
     * 设置信息
     * 
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @Apidoc\Returned("file_types", type="object", desc="文件类型")
     * @Apidoc\Returned("storages", type="object", desc="存储方式")
     * @Apidoc\Returned("accept_ext", type="string", desc="允许上传的文件后缀")
     * @return array
     */
    public static function info($fields = '')
    {
        $id = self::$id;

        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();
            $info['file_types'] = self::fileTypes();
            $info['storages']   = self::storages();
            $info['accept_ext'] = self::fileAccept($info);

            $info = array_merge($info, self::fileType('', true));

            SettingCache::set($id, $info);
        }

        if ($fields) {
            $data = [];
            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                $field = trim($field);
                if (isset($info[$field])) {
                    $data[$field] = $info[$field];
                }
            }
            return $data;
        }

        return $info;
    }

    /**
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @return array|Exception
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
     * 文件类型数组或描述
     * 
     * @param string $type 文件类型
     *
     * @return array|string 
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
     * 文件储存方式数组或描述
     * 
     * @param string $storage 储存方式
     *
     * @return array|string
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
     * @param int  $file_size 文件大小字节数
     * @param int  $precision 保留小数位数
     * @param bool $is_space  是否使用空格分隔符
     *
     * @return string
     */
    public static function fileSize($file_size, $precision = 2, $is_space = false)
    {
        $units      = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB', 'BB');
        $file_size  = max($file_size, 0);
        $pow        = floor(log($file_size) / log(1024));
        $pow        = max($pow, 0);
        $file_size /= pow(1024, $pow);

        $separator = '';
        if ($is_space) {
            $separator = ' ';
        }

        return round($file_size, $precision) . $separator . $units[$pow];
    }

    /**
     * 文件类型获取
     *
     * @param string $file_ext 文件后缀
     * @param bool   $get_exts 获取支持后缀
     *
     * @return string image图片，video视频，audio音频，word文档，other其它
     */
    public static function fileType($file_ext = '', $get_exts = false)
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

        if ($get_exts) {
            return [
                'image_exts' => '支持图片格式：'. implode('，', $image_ext),
                'video_exts' => '支持视频格式：'. implode('，', $video_ext),
                'audio_exts' => '支持音频格式：'. implode('，', $audio_ext),
                'word_exts'  => '支持文档格式：'. implode('，', $word_ext),
                'other_exts' => '除图片、视频、音频、文档的其他格式',
            ];
        }

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
            $domain    = $file['domain'];
            $file_path = $file['file_path'];
            if ($file['storage'] == 'local' && empty($domain)) {
                $file_url = file_url($file_path);
            } else {
                $domain    = rtrim($domain, '/');
                $file_path = ltrim($file_path, '/');
                $file_url  = $domain . '/' . $file_path;
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
