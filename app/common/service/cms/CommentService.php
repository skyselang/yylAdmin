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
            $field = $pk . ',call,mobile,tel,title,remark,is_unread,create_time,update_time,delete_time';
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
     * @param $id 留言id
     * 
     * @return array|Exception
     */
    public static function info($id)
    {
        $info = CommentCache::get($id);
        if (empty($info)) {
            $model = new CommentModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                exception('留言不存在：' . $id);
            }
            $info = $info->toArray();

            if ($info['is_unread']) {
                $update['is_unread'] = 0;
                $update['read_time'] = $info['read_time'] = datetime();
                $model->where($pk, $id)->update($update);
            }
        }

        return $info;
    }

    /**
     * 留言添加
     *
     * @param $param 留言信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 留言修改 
     *     
     * @param $param 留言信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        CommentCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 留言删除
     * 
     * @param array $ids 留言id
     * 
     * @return array|Exception
     */
    public static function dele($ids)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            CommentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 留言已读
     *
     * @param array $ids 留言id
     * 
     * @return array
     */
    public static function isread($ids)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $update['is_unread'] = 0;
        $update['read_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->where('is_unread', 1)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            CommentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 留言回收站恢复
     * 
     * @param array $ids 留言id
     * 
     * @return array|Exception
     */
    public static function recoverReco($ids)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            CommentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 留言回收站删除
     * 
     * @param array $ids 留言id
     * 
     * @return array|Exception
     */
    public static function recoverDele($ids)
    {
        $model = new CommentModel();
        $pk = $model->getPk();

        $res = $model->where($pk, 'in', $ids)->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            CommentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}
