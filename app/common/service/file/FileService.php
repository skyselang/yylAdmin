<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件管理
namespace app\common\service\file;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\file\FileCache;

class FileService
{
    /**
     * 文件列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'file_id,group_id,file_type,file_name,file_path,file_size,file_ext,sort,is_disable';
        }

        if (empty($order)) {
            $order = ['update_time' => 'desc', 'sort' => 'desc', 'file_id' => 'desc'];
        }

        $count = Db::name('file')
            ->where($where)
            ->count('file_id');

        $list = Db::name('file')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        foreach ($list as $k => $v) {
            if (isset($v['file_path'])) {
                $list[$k]['file_url'] = file_url($v['file_path']);
            }
            if (isset($v['file_size'])) {
                $list[$k]['file_size'] = self::sizeFormat($v['file_size']);
            }
        }

        $file_ids = array_column($list, 'file_id');

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;
        $data['ids']   = $file_ids;

        return $data;
    }

    /**
     * 文件信息
     *
     * @param integer $file_id 文件id
     * 
     * @return array
     */
    public static function info($file_id = '')
    {
        $file = FileCache::get($file_id);
        if (empty($file)) {
            $file = Db::name('file')
                ->where('file_id', $file_id)
                ->find();
            if ($file) {
                if ($file['storage'] == 'local') {
                    $file['file_url'] = file_url($file['file_path']);
                } else {
                    $file['file_url'] = $file['domain'] . '/' . $file['file_hash'] . '.' . $file['file_ext'];
                }

                $file['file_size'] = self::sizeFormat($file['file_size']);

                FileCache::set($file_id, $file);
            }
        }

        return $file;
    }

    /**
     * 文件添加
     *
     * @param array $param 文件信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $file = $param['file'];
        unset($param['file']);
        $datetime = datetime();

        $file_ext  = $file->getOriginalExtension();
        $file_type = self::typeJudge($file_ext);
        $file_size = $file->getSize();
        $file_hash = $file->hash('sha1');
        $file_name = Filesystem::disk('public')
            ->putFile('file', $file, function () use ($file_hash) {
                return $file_hash;
            });

        $param['file_hash'] = $file_hash;
        $param['file_path'] = 'storage/' . $file_name;
        $param['file_ext']  = $file_ext;
        $param['file_size'] = $file_size;
        $param['file_type'] = $file_type;
        if (empty($param['file_name'])) {
            $param['file_name'] = mb_substr($file->getOriginalName(), 0, - (mb_strlen($param['file_ext']) + 1));
        }

        // 对象存储
        $param = StorageService::upload($param);

        $file_exist = Db::name('file')
            ->field('file_id')
            ->where('file_hash', '=', $file_hash)
            ->find();
        if ($file_exist) {
            $file_update['file_id']    = $file_exist['file_id'];
            $file_update['storage']    = $param['storage'];
            $file_update['domain']     = $param['domain'];
            $file_update['is_disable'] = 0;
            $file_update['is_delete']  = 0;
            self::edit($file_update);
            $file_id = $file_exist['file_id'];
        } else {
            $param['create_time'] = $datetime;
            $param['update_time'] = $datetime;
            $file_id = Db::name('file')
                ->strict(false)
                ->insertGetId($param);
            if (empty($file_id)) {
                exception();
            }
        }

        $file_info = self::info($file_id);

        return $file_info;
    }

    /**
     * 文件修改
     *
     * @param array $param 文件信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $file_id = $param['file_id'];
        unset($param['file_id']);
        $param['update_time'] = datetime();

        $res = Db::name('file')
            ->where('file_id', '=', $file_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param['file_id'] = $file_id;

        FileCache::del($file_id);

        return $param;
    }

    /**
     * 文件删除
     *
     * @param array $file_ids  文件id数组
     * @param int   $is_delete 是否删除
     * 
     * @return array
     */
    public static function dele($file_ids, $is_delete = 1)
    {
        $update['is_delete']   = $is_delete;
        $update['delete_time'] = datetime();

        $res = Db::name('file')
            ->where('file_id', 'in', $file_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['file_ids'] = $file_ids;

        foreach ($file_ids as $k => $v) {
            FileCache::del($v);
        }

        return $update;
    }

    /**
     * 文件是否禁用
     *
     * @param array $file_ids   文件id数组
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($file_ids, $is_disable = 0)
    {
        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name('file')
            ->where('file_id', 'in', $file_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['file_ids'] = $file_ids;

        foreach ($file_ids as $k => $v) {
            FileCache::del($v);
        }

        return $update;
    }

    /**
     * 文件修改分组
     *
     * @param array $file_ids 文件id数组
     * @param int   $group_id 分组id
     * 
     * @return array
     */
    public static function group($file_ids, $group_id = 0)
    {
        $update['group_id']    = $group_id;
        $update['update_time'] = datetime();

        $res = Db::name('file')
            ->where('file_id', 'in', $file_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['file_ids'] = $file_ids;

        foreach ($file_ids as $k => $v) {
            FileCache::del($v);
        }

        return $update;
    }

    /**
     * 文件回收站恢复
     * 
     * @param array $file_ids 文件id数组
     * 
     * @return array|Exception
     */
    public static function recoverReco($file_ids)
    {
        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('file')
            ->where('file_id', 'in', $file_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($file_ids as $k => $v) {
            FileCache::del($v);
        }

        $update['file_ids'] = $file_ids;

        return $update;
    }

    /**
     * 文件回收站删除
     * 
     * @param array $file_ids 文件id数组
     * 
     * @return array|Exception
     */
    public static function recoverDele($file_ids)
    {
        $file = Db::name('file')
            ->field('file_id,file_path')
            ->where('file_id', 'in', $file_ids)
            ->select();

        $res = Db::name('file')
            ->where('file_id', 'in', $file_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        $file_del = [];
        foreach ($file as $k => $v) {
            FileCache::del($v['file_id']);
            $file_del[] = @unlink($v['file_path']);
        }

        $update['file_ids'] = $file_ids;
        $update['file_del'] = $file_del;

        return $update;
    }

    /**
     * 文件链接
     *
     * @param integer $file_id 文件id
     *
     * @return string
     */
    public static function fileUrl($file_id)
    {
        $file = self::info($file_id);
        $file_url = $file['file_url'];

        return $file_url;
    }

    /**
     * 文件数组
     *
     * @param string $file_ids 文件id，逗号,隔开
     *
     * @return array
     */
    public static function fileArray($file_ids)
    {
        $file = Db::name('file')
            ->field('file_id,file_type,file_name,file_path,file_size')
            ->where('file_id', 'in', $file_ids)
            ->where('is_disable', '=', 0)
            ->select()
            ->toArray();
        foreach ($file as $k => $v) {
            $file[$k]['file_url']  = file_url($v['file_path']);
            $file[$k]['file_size'] = self::sizeFormat($v['file_size']);
        }

        return $file;
    }

    /**
     * 文件类型
     *
     * @return array
     */
    public static function fileType()
    {
        $filetype = [
            'image' => '图片',
            'video' => '视频',
            'audio' => '音频',
            'word'  => '文档',
            'other' => '其它'
        ];

        return $filetype;
    }

    /**
     * 文件储存方式
     *
     * @return array
     */
    public static function storage()
    {
        $storage = [
            'local'   => '本地(服务器)',
            'qiniu'   => '七牛云Kodo',
            'aliyun'  => '阿里云OSS',
            'tencent' => '腾讯云COS',
            'baidu'   => '百度云BOS'
        ];

        return $storage;
    }

    /**
     * 文件大小格式化
     *
     * @param integer $file_size 文件大小（byte(B)字节）
     *
     * @return string
     */
    public static function sizeFormat($file_size = 0)
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

        return number_format($file_size, 2) . ' ' . $format;
    }

    /**
     * 文件类型判断
     *
     * @param string $file_ext 文件后缀
     *
     * @return string image图片，video视频，audio音频，word文档，other其它
     */
    public static function typeJudge($file_ext = '')
    {
        if ($file_ext) {
            $file_ext = strtolower($file_ext);
        }

        $image_ext = [
            'jpg', 'png', 'jpeg', 'gif', 'bmp', 'webp', 'ico', 'svg', 'tif', 'pcx', 'tga', 'exif',
            'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'raw', 'wmf',  'avif', 'apng', 'xbm', 'fpx'
        ];
        $video_ext = [
            'mp4', 'avi', 'mkv', 'flv', 'rm', 'rmvb', 'webm', '3gp', 'mpeg', 'mpg', 'dat', 'asx', 'wmv',
            'mov', 'm4a', 'ogm', 'vob'
        ];
        $audio_ext = ['mp3', 'aac', 'wma', 'wav', 'ape', 'flac', 'ogg', 'adt', 'adts', 'cda'];
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
     * 文件统计
     *   
     * @return array
     */
    public static function statistics()
    {
        $data = [];
        $data['count'] = Db::name('file')->where('is_delete', 0)->count('file_id');

        $file_type = self::fileType();
        foreach ($file_type as $k => $v) {
            $tmp['name']  = $v;
            $tmp['value'] = Db::name('file')->where('file_type', $k)->where('is_delete', 0)->count('file_id');
            $data['data'][] = $tmp;
        }

        return $data;
    }
}
