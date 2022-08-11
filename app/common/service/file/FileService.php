<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use think\facade\Filesystem;
use app\common\cache\file\FileCache;
use app\common\model\file\FileModel;

/**
 * 文件管理
 */
class FileService
{
    /**
     * 文件列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        $model = new FileModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',storage,domain,file_type,file_hash,file_name,file_path,file_size,file_ext,sort,is_disable';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', 'is_disable' => 'asc', 'update_time' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        $ids = [];
        foreach ($list as $k => $v) {
            $ids[] = $v['file_id'];
            $list[$k]['file_url'] = SettingService::fileUrl($v);
            $list[$k]['file_size'] = SettingService::fileSize($v['file_size']);
        }

        $storage  = SettingService::storage();
        $filetype = SettingService::fileType();
        $setting  = SettingService::info('limit_max,accept_ext');
        $group    = GroupService::list([['is_disable', '=', 0], ['is_delete', '=', 0]], 1, 9999, [], 'group_id,group_name')['list'];

        return compact('count', 'pages', 'page', 'limit', 'list', 'ids', 'storage', 'filetype', 'setting', 'group');
    }

    /**
     * 文件信息
     *
     * @param int  $id   文件id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = FileCache::get($id);
        if (empty($info)) {
            $model = new FileModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('文件不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $info['file_url'] = SettingService::fileUrl($info);
            $info['file_size'] = SettingService::fileSize($info['file_size']);

            FileCache::set($id, $info);
        }

        return $info;
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
        $setting = SettingService::info();
        if (!$setting['is_open']) {
            exception('文件上传未开启，无法上传文件！');
        }

        $file = $param['file'];
        unset($param['file']);
        $datetime = datetime();

        $file_ext  = $file->getOriginalExtension();
        $file_type = SettingService::getFileType($file_ext);
        $file_size = $file->getSize();
        $file_md5  = $file->hash('md5');
        $file_hash = $file->hash('sha1');
        $file_name = Filesystem::disk('public')
            ->putFile('file', $file, function () use ($file_hash) {
                return date('Ymd') . '/' . $file_hash;
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

        $model = new FileModel();
        $pk = $model->getPk();
        $file_exist = $model->field($pk)->where('file_hash', $file_hash)->find();
        if ($file_exist) {
            $file_exist = $file_exist->toArray();
            $param[$pk] = $file_exist[$pk];
        } else {
            $param[$pk] = '';
        }

        // 对象存储
        $param = StorageService::upload($param);

        if ($file_exist) {
            $param[$pk]          = $file_exist[$pk];
            $param['storage']    = $param['storage'];
            $param['domain']     = $param['domain'];
            $param['is_disable'] = 0;
            $param['is_delete']  = 0;
            self::edit([$file_exist[$pk]], $param);
            $id = $file_exist[$pk];
        } else {
            unset($param[$pk]);
            $param['create_time'] = $datetime;
            $param['update_time'] = $datetime;
            $id = $model->strict(false)->insertGetId($param);
            if (empty($id)) {
                exception();
            }
        }

        $info = self::info($id);

        return $info;
    }

    /**
     * 文件修改
     *
     * @param array $ids    文件id
     * @param array $update 文件信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $update = [])
    {
        $model = new FileModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        FileCache::del($ids);

        return $update;
    }

    /**
     * 文件删除
     *
     * @param array $ids  文件id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $file = [];
        if ($real) {
            $file = $model->field('file_path')->where($pk, 'in', $ids)->select();
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        FileCache::del($ids);

        foreach ($file as $v) {
            @unlink($v['file_path']);
        }

        return $update;
    }

    /**
     * 文件链接
     *
     * @param mixed $file 文件id、信息
     *
     * @return string
     */
    public static function fileUrl($file)
    {
        if (is_numeric($file)) {
            $file = self::info($file, false);
        }

        $file_url = '';
        if ($file) {
            if ($file['is_disable'] == 0 && $file['is_delete'] == 0) {
                $file_url = SettingService::fileUrl($file);
            }
        }

        return $file_url;
    }

    /**
     * 文件数组
     *
     * @param string $ids 文件id，逗号,隔开
     *
     * @return array
     */
    public static function fileArray($ids = '')
    {
        if (is_array($ids)) {
            $ids = implode(',', array_unique(array_filter($ids)));
        }

        if (empty($ids)) {
            return [];
        }

        $model = new FileModel();
        $pk = $model->getPk();

        $field = $pk . ',storage,domain,file_name,file_size,file_hash,file_path,file_ext';
        $where = [[$pk, 'in', $ids], ['is_disable', '=', 0], ['is_delete', '=', 0]];
        $order = "field(file_id," . $ids . ")";

        $file = $model->field($field)->where($where)->orderRaw($order)->select()->toArray();
        foreach ($file as $k => $v) {
            $file[$k]['file_url'] = SettingService::fileurl($v);
            $file[$k]['file_size'] = SettingService::fileSize($v['file_size']);
        }

        return $file;
    }

    /**
     * 文件统计
     *   
     * @return array
     */
    public static function statistics()
    {
        $key = 'count';
        $data = FileCache::get($key);
        if (empty($data)) {
            $model = new FileModel();

            $file_types = SettingService::fileType();
            $file_field = 'file_type,count(file_type) as count';
            $file_count = $model->field($file_field)->where('is_delete', 0)->group('file_type')->select()->toArray();
            foreach ($file_types as $k => $v) {
                $temp = [];
                $temp['name']  = $v;
                $temp['value'] = 0;
                foreach ($file_count as $vfc) {
                    if ($k == $vfc['file_type']) {
                        $temp['value'] = $vfc['count'];
                    }
                }
                $data['data'][] = $temp;
            }
            $data['count'] = $model->where('is_delete', 0)->count();

            FileCache::set($key, $data);
        }

        return $data;
    }
}
