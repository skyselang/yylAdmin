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
    // 表名
    protected static $t_name = 'file';
    // 表主键
    protected static $t_pk = 'file_id';

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
            $field = self::$t_pk . ',group_id,storage,domain,file_md5,file_hash,file_type,file_name,file_path,file_size,file_ext,sort,is_disable';
        } else {
            $field = str_merge($field, 'file_id,storage,domain,file_md5,file_hash,file_path,file_ext');
        }

        if (empty($order)) {
            $order = ['update_time' => 'desc', 'sort' => 'desc', self::$t_pk => 'desc'];
        }

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $pages = ceil($count / $limit);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['file_url'] = '';
            if ($v['storage'] == 'local') {
                $list[$k]['file_url'] = file_url($v['file_path']);
            } else {
                $list[$k]['file_url'] = $v['domain'] . '/' . $v['file_hash'] . '.' . $v['file_ext'];
            }

            if (isset($v['file_size'])) {
                $list[$k]['file_size'] = self::sizeFormat($v['file_size']);
            }
        }

        $ids = array_column($list, self::$t_pk);

        return compact('count', 'pages', 'page', 'limit', 'list', 'ids');
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
        if (empty($file_id)) {
            return [];
        }

        $file = FileCache::get($file_id);
        if (empty($file)) {
            $file = Db::name(self::$t_name)
                ->where(self::$t_pk, $file_id)
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
     * @return array|Exception
     */
    public static function add($param)
    {
        $file = $param['file'];
        unset($param['file']);
        $datetime = datetime();

        $file_ext  = $file->getOriginalExtension();
        $file_type = self::typeJudge($file_ext);
        $file_size = $file->getSize();
        $file_md5  = $file->hash('md5');
        $file_hash = $file->hash('sha1');
        $file_name = Filesystem::disk('public')
            ->putFile('file', $file, function () use ($file_hash) {
                return $file_hash;
            });

        $param['file_md5']  = $file_md5;
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

        $file_exist = Db::name(self::$t_name)
            ->field(self::$t_pk)
            ->where('file_hash', '=', $file_hash)
            ->find();
        if ($file_exist) {
            $file_update[self::$t_pk]  = $file_exist[self::$t_pk];
            $file_update['storage']    = $param['storage'];
            $file_update['domain']     = $param['domain'];
            $file_update['is_disable'] = 0;
            $file_update['is_delete']  = 0;
            self::edit($file_update);
            $file_id = $file_exist[self::$t_pk];
        } else {
            $param['create_time'] = $datetime;
            $param['update_time'] = $datetime;
            $file_id = Db::name(self::$t_name)
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
     * @return array|Exception
     */
    public static function edit($param)
    {
        $file_id = $param[self::$t_pk];
        unset($param[self::$t_pk]);
        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, '=', $file_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $file_id;

        FileCache::del($file_id);

        return $param;
    }

    /**
     * 文件删除
     *
     * @param array   $file_ids  文件id数组
     * @param integer $is_delete 是否删除
     * 
     * @return array|Exception
     */
    public static function dele($file_ids, $is_delete = 1)
    {
        $update['is_delete']   = $is_delete;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $file_ids)
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
     * @param array   $file_ids   文件id数组
     * @param integer $is_disable 是否禁用
     * 
     * @return array|Exception
     */
    public static function disable($file_ids, $is_disable = 0)
    {
        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $file_ids)
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
     * @param array   $file_ids 文件id数组
     * @param integer $group_id 分组id
     * 
     * @return array|Exception
     */
    public static function group($file_ids, $group_id = 0)
    {
        $update['group_id']    = $group_id;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $file_ids)
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

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $file_ids)
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
        $file = Db::name(self::$t_name)
            ->field('file_id,file_path')
            ->where(self::$t_pk, 'in', $file_ids)
            ->select();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $file_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        $file_del = [];
        foreach ($file as $k => $v) {
            FileCache::del($v[self::$t_pk]);
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
        if ($file) {
            return $file['file_url'];
        }

        return '';
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
        $file = Db::name(self::$t_name)
            ->field('file_id,file_type,file_name,file_path,file_size')
            ->where(self::$t_pk, 'in', $file_ids)
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
        $key  = 'count';
        $data = FileCache::get($key);
        if (empty($data)) {
            $data['count'] = Db::name(self::$t_name)->where('is_delete', 0)->count(self::$t_pk);

            $file_type = self::fileType();
            $file_count = Db::name(self::$t_name)
                ->field('file_type,count(file_type) as file_count')
                ->where('is_delete', 0)
                ->group('file_type')
                ->select()
                ->toArray();
            foreach ($file_type as $k => $v) {
                $temp = [];
                $temp['name']  = $v;
                $temp['value'] = 0;
                foreach ($file_count as $kf => $vf) {
                    if ($k == $vf['file_type']) {
                        $temp['value'] = $vf['file_count'];
                    }
                }
                $data['data'][] = $temp;
            }

            FileCache::set($key, $data);
        }

        return $data;
    }
}
