<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use app\common\cache\file\TagCache;
use app\common\cache\file\FileCache;
use app\common\model\file\TagModel;
use app\common\model\file\TagsModel;

/**
 * 文件标签
 */
class TagService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'tag_id/d'     => '',
        'tag_unique/s' => '',
        'tag_name/s'   => '',
        'tag_desc/s'   => '',
        'remark/s'     => '',
        'sort/d'       => 250,
    ];

    /**
     * 文件标签列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * 
     * @return array ['count', 'pages', 'page', 'limit', 'list']
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new TagModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',tag_unique,tag_name,tag_desc,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $append = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }

        $count = $pages = 0;
        if ($total) {
            $count_model = clone $model;
            $count = $count_model->where($where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list = $model->field($field)->where($where)->append($append)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 文件标签信息
     *
     * @param int|string $id   标签id
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = TagCache::get($id);
        if (empty($info)) {
            $model = new TagModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['tag_unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('文件标签不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            TagCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 文件标签添加
     *
     * @param array $param 标签信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new TagModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        if (empty($param['tag_unique'] ?? '')) {
            $param['tag_unique'] = uniqids();
        }

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 文件标签修改
     *
     * @param int|array $ids   标签id
     * @param array     $param 标签信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new TagModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $unique = $model->where($pk, 'in', $ids)->column('tag_unique');

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        TagCache::del($ids);
        TagCache::del($unique);

        return $param;
    }

    /**
     * 文件标签删除
     *
     * @param array $ids  标签id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new TagModel();
        $pk = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('tag_unique');

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = delete_update();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        TagCache::del($ids);
        TagCache::del($unique);

        return $update;
    }

    /**
     * 文件标签文件列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function file($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return FileService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 文件标签文件解除
     *
     * @param array $tag_id   标签id
     * @param array $file_ids 文件id
     *
     * @return int
     */
    public static function fileRemove($tag_id, $file_ids = [])
    {
        $where[] = ['tag_id', 'in', $tag_id];
        if (empty($file_ids)) {
            $file_ids = TagsModel::where($where)->column('file_id');
        }
        $where[] = ['file_id', 'in', $file_ids];

        $res = TagsModel::where($where)->delete();

        FileCache::del($file_ids);

        return $res;
    }
}
