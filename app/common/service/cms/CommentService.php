<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 留言管理
namespace app\common\service\cms;

use app\common\cache\cms\CommentCache;
use app\common\model\cms\CommentModel;

class CommentService
{
    /**
     * 留言列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',call,mobile,tel,title,remark,is_unread,read_time,create_time,update_time,delete_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list  = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 留言信息
     * 
     * @param int $id 留言id
     * 
     * @return array|Exception
     */
    public static function info($id)
    {
        $info = CommentCache::get($id);
        if (empty($info)) {
            $model = new CommentModel();

            $info = $model->find($id);
            if (empty($info)) {
                exception('留言不存在：' . $id);
            }
            $info = $info->toArray();

            if ($info['is_unread']) {
                $update['is_unread'] = 0;
                $update['read_time'] = $info['read_time'] = datetime();
                self::edit($id, $update);
            }
        }

        return $info;
    }

    /**
     * 留言添加
     *
     * @param array $insert 留言信息
     *
     * @return array|Exception
     */
    public static function add($insert)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $insert['create_time'] = datetime();
        $id = $model->insertGetId($insert);
        if (empty($id)) {
            exception();
        }

        $insert[$pk] = $id;

        return $insert;
    }

    /**
     * 留言修改 
     *     
     * @param mixed $ids    留言id
     * @param array $update 留言信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $update = [])
    {
        $model = new CommentModel();
        $pk = $model->getPk();
        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();
        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        CommentCache::del($ids);
        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 留言删除
     * 
     * @param mixed $ids  留言id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        CommentCache::del($ids);
        $update['ids'] = $ids;

        return $update;
    }
}
