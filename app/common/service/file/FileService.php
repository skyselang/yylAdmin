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

use think\facade\Filesystem;
use app\common\cache\file\FileCache;
use app\common\model\file\FileModel;
use app\common\model\file\GroupModel;

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

        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        if (empty($field)) {
            $field = $pk . ',' . $GroupPk . ',storage,domain,file_md5,file_hash,file_type,file_name,file_path,file_size,file_ext,sort,is_disable';
        } else {
            $field = str_merge($field, 'file_id,storage,domain,file_md5,file_hash,file_path,file_ext,is_disable');
        }

        if (empty($order)) {
            $order = ['update_time' => 'desc', 'sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['file_url'] = self::fileUrl($v);
            if (isset($v['file_size'])) {
                $list[$k]['file_size'] = SettingService::fileSize($v['file_size']);
            }
        }

        $ids = array_column($list, $pk);

        return compact('count', 'pages', 'page', 'limit', 'list', 'ids');
    }

    /**
     * 文件信息
     *
     * @param int $id 文件id
     * 
     * @return array
     */
    public static function info($id)
    {
        if (empty($id)) {
            return [];
        }

        $info = FileCache::get($id);
        if (empty($info)) {
            $model = new FileModel();
            $info = $model->find($id);
            if (empty($info)) {
                return [];
            } else {
                $info = $info->toArray();
                if ($info['is_disable']) {
                    $info['file_url'] = '';
                } else {
                    $info['file_url'] = self::fileUrl($info);
                }
                $info['file_size'] = SettingService::fileSize($info['file_size']);

                FileCache::set($id, $info);
            }
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

        $model = new FileModel();
        $pk = $model->getPk();

        $file_exist = $model->field($pk)->where('file_hash', $file_hash)->find();
        if ($file_exist) {
            $file_exist = $file_exist->toArray();
            $file_update[$pk]          = $file_exist[$pk];
            $file_update['storage']    = $param['storage'];
            $file_update['domain']     = $param['domain'];
            $file_update['is_disable'] = 0;
            $file_update['is_delete']  = 0;
            self::edit($file_update);
            $id = $file_exist[$pk];
        } else {
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
     * @param array $param 文件信息
     * 
     * @return array|Exception
     */
    public static function edit($param)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        FileCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 文件删除
     *
     * @param array $ids       文件id
     * @param int   $is_delete 是否删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $is_delete = 1)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['is_delete']   = $is_delete;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件修改分组
     *
     * @param array $ids      文件id
     * @param int   $group_id 分组id
     * 
     * @return array|Exception
     */
    public static function editgroup($ids, $group_id = 0)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['group_id']    = $group_id;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件修改类型
     *
     * @param array  $ids       文件id
     * @param string $file_type 文件类型
     * 
     * @return array|Exception
     */
    public static function edittype($ids, $file_type = 'image')
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['file_type']   = $file_type;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件修改域名
     *
     * @param array  $ids    文件id
     * @param string $domain 文件域名
     * 
     * @return array|Exception
     */
    public static function editdomain($ids, $domain = '')
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['domain']      = $domain;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件是否禁用
     *
     * @param array $ids        文件id
     * @param int   $is_disable 是否禁用
     * 
     * @return array|Exception
     */
    public static function disable($ids, $is_disable = 0)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }


    /**
     * 文件回收站恢复
     * 
     * @param array $ids 文件id
     * 
     * @return array|Exception
     */
    public static function recoverReco($ids)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件回收站删除
     * 
     * @param array $ids 文件id
     * 
     * @return array|Exception
     */
    public static function recoverDele($ids)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $file = $model->field($pk . ',file_path')->where($pk, 'in', $ids)->select();

        $res = $model->where($pk, 'in', $ids)->delete();
        if (empty($res)) {
            exception();
        }

        $del = [];
        foreach ($file as $v) {
            FileCache::del($v[$pk]);
            $del[] = @unlink($v['file_path']);
        }

        $update['ids'] = $ids;
        $update['del'] = $del;

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
            $file = self::info($file);
        }

        $file_url = '';
        if ($file) {
            if (!$file['is_disable']) {
                if ($file['storage'] == 'local') {
                    $file_url = file_url($file['file_path']);
                } else {
                    $file_url = $file['domain'] . '/' . $file['file_hash'] . '.' . $file['file_ext'];
                }
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
        if (empty($ids)) {
            return [];
        }

        $file = [];
        $ids = explode(',', $ids);
        foreach ($ids as $v) {
            $info = self::info($v);
            if ($info) {
                $file[] = $info;
            }
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
            $pk = $model->getPk();

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
            $data['count'] = $model->where('is_delete', 0)->count($pk);

            FileCache::set($key, $data);
        }

        return $data;
    }
}
