<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\LinkCache;
use app\common\model\setting\LinkModel;

/**
 * 友链管理
 */
class LinkService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'link_id/d'    => '',
        'unique/s'     => '',
        'image_id/d'   => 0,
        'name/s'       => '',
        'name_color/s' => '#606266',
        'url/s'        => '',
        'desc/s'       => '',
        'start_time/s' => '',
        'end_time/s'   => '',
        'underline/s'  => '',
        'remark/s'     => '',
        'sort/d'       => 250,
    ];

    /**
     * 友链列表
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
        $model = new LinkModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',unique,image_id,name,name_color,url,desc,start_time,end_time,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'image_id')) {
            $with[]   = $hidden[] = 'image';
            $append[] = 'image_url';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

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
        $list = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 友链信息
     * 
     * @param int|string $id   友链id、标识
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = LinkCache::get($id);
        if (empty($info)) {
            $model = new LinkModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('友链不存在：' . $id);
                }
                return [];
            }
            $info = $info->append(['image_url'])->hidden(['image'])->toArray();

            LinkCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 友链添加
     *
     * @param array $param 友链信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new LinkModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
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
     * 友链修改
     *     
     * @param int|array $ids   友链id
     * @param array     $param 友链信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new LinkModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        LinkCache::del($ids);
        LinkCache::del($unique);

        return $param;
    }

    /**
     * 友链删除
     * 
     * @param array $ids  友链id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new LinkModel();
        $pk = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
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

        LinkCache::del($ids);
        LinkCache::del($unique);

        return $update;
    }
}
