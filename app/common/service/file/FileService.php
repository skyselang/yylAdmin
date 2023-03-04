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
use app\common\service\file\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 文件管理
 */
class FileService
{
    /**
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
        'file_id/d'   => 0,
        'file_name/s' => '',
        'group_id/d'  => 0,
        'tag_ids/a'   => [],
        'file_type/s' => 'image',
        'domain/s'    => '',
        'sort/d'      => 250
    ];

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
            $field = 'm.' . $pk . ',group_id,storage,domain,file_type,file_hash,file_name,file_path,file_size,file_ext,sort,is_disable,create_time,update_time,delete_time';
        }
        if (empty($order)) {
            $order = ['update_time' => 'desc', $pk => 'desc'];
        }

        $model = $model->alias('m');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'tag_ids' && is_array($wv[2])) {
                $model = $model->join('file_tags t', 'm.file_id=t.file_id', 'left')->where('t.tag_id', 'in', $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->with(['group', 'tags'])
            ->append(['group_name', 'tag_names', 'file_type_name', 'file_url', 'file_size'])
            ->hidden(['group', 'tags'])
            ->page($page)->limit($limit)->order($order)->select()->toArray();

        $ids = array_column($list, $pk);
        $storage = SettingService::storages();
        $filetype = SettingService::fileTypes();
        $setting = SettingService::info('limit_max,accept_ext');

        return compact('count', 'pages', 'page', 'limit', 'list', 'ids', 'storage', 'filetype', 'setting');
    }

    /**
     * 文件信息
     *
     * @param int  $id   文件id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
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
            $info = $info->append(['group_name', 'tag_names', 'file_type_name', 'file_url', 'file_size'])->toArray();

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
        $model = new FileModel();
        $pk = $model->getPk();

        $datetime = datetime();
        $type = $param['type'] ?? 'upl';
        if ($type == 'url') {
            $url    = $param['file_url'];
            $file   = parse_url($url);
            $scheme = $file['scheme'] ?? '';
            $port   = $file['port'] ?? '';
            $host   = $file['host'] ?? '';
            $path   = $file['path'] ?? '';
            $query  = $file['query'] ?? '';

            $param['domain']    = $scheme . '://' . $host . ($port ? ':' . $port : '');
            $param['file_path'] = $path . ($query ? '?' . $query : '');
            $param['file_ext']  = substr(strrchr($path, '.'), 1);
            if (empty($param['file_name'])) {
                $param['file_name'] = substr(strrchr($path, '/'), 0, - (strlen($param['file_ext']) + 1));
            }
            $param['file_name'] = trim($param['file_name'], '/');

            $file_exist = $model->field($pk)->where('domain', $param['domain'])->where('file_path', $param['file_path'])->find();
        } else {
            $file = $param['file'];
            unset($param['file']);

            $file_ext  = strtolower($file->getOriginalExtension());
            $file_type = SettingService::fileType($file_ext);
            $file_size = $file->getSize();
            $file_md5  = $file->hash('md5');
            $file_hash = $file->hash('sha1');
            $file_name = Filesystem::disk('public')
                ->putFile('file', $file, function () use ($file_hash) {
                    return date('Ymd') . '/' . $file_hash;
                });

            $param['file_md5']   = $file_md5;
            $param['file_hash']  = $file_hash;
            $param['file_path']  = 'storage/' . $file_name;
            $param['file_ext']   = $file_ext;
            $param['file_size']  = $file_size;
            $param['file_type']  = $file_type;
            if (empty($param['file_name'])) {
                $param['file_name'] = mb_substr($file->getOriginalName(), 0, - (mb_strlen($param['file_ext']) + 1));
            }

            $file_exist = $model->field($pk)->where('file_hash', $file_hash)->find();
            if ($file_exist) {
                $file_exist = $file_exist->toArray();
                $param[$pk] = $file_exist[$pk];
            } else {
                $param[$pk] = '';
            }

            // 对象存储
            $param = StorageService::upload($param, $pk);
        }

        if ($file_exist) {
            $param['is_disable'] = 0;
            $param['is_delete']  = 0;
            $id = $file_exist[$pk];
            self::edit($id, $param);
        } else {
            unset($param[$pk]);
            $param['create_uid']  = user_id();
            $param['create_time'] = $datetime;
            $param['update_time'] = $datetime;
            $model->save($param);
            // 标签
            if (isset($param['tag_ids'])) {
                $model->tags()->saveAll($param['tag_ids']);
            }
            $id = $model->$pk;
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
     * @param int|array $ids   文件id
     * @param array     $param 文件信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new FileModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (isset($param['tag_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        $info->tags()->detach();
                        $info->tags()->saveAll($param['tag_ids']);
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        FileCache::del($ids);

        return $param;
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

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                $file = $model->field($pk . ',file_path')->where($pk, 'in', $ids)->select();
                foreach ($file as $v) {
                    $info = $model->find($v[$pk]);
                    // 删除标签
                    $info->tags()->detach();
                    // 删除文件
                    try {
                        unlink($v['file_path']);
                    } catch (\Exception $e) {
                    }
                }
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        FileCache::del($ids);

        return $update;
    }

    /**
     * 文件统计
     * 
     * @Apidoc\Returned("count", type="int", desc="文件总数")
     * @Apidoc\Returned("data", type="array", desc="图表series.data")
     * 
     * @return array
     */
    public static function statistic()
    {
        $key = 'statistic';
        $data = FileCache::get($key);
        if (empty($data)) {
            $model = new FileModel();

            $file_types = SettingService::fileTypes();
            $file_field = 'file_type,count(file_type) as count';
            $file_count = $model->field($file_field)->where([where_delete()])->group('file_type')->select()->toArray();
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
            $data['count'] = $model->where([where_delete()])->count();

            FileCache::set($key, $data);
        }

        return $data;
    }
}
