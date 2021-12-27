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

use think\facade\Db;
use app\common\cache\cms\CommentCache;

class CommentService
{
    // 表名
    protected static $t_name = 'cms_comment';
    // 表主键
    protected static $t_pk = 'comment_id';

    /**
     * 留言列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = self::$t_pk . ',call,mobile,tel,title,remark,is_unread,create_time,update_time,delete_time';
        }

        if (empty($order)) {
            $order = [self::$t_pk => 'desc'];
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

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 留言信息
     * 
     * @param $comment_id 留言id
     * 
     * @return array|Exception
     */
    public static function info($comment_id)
    {
        $comment = CommentCache::get($comment_id);
        if (empty($comment)) {
            $comment = Db::name(self::$t_name)
                ->where(self::$t_pk, $comment_id)
                ->find();
            if (empty($comment)) {
                exception('留言不存在：' . $comment_id);
            }
            if ($comment['is_unread']) {
                $update['is_unread'] = 0;
                $update['read_time'] = $comment['read_time'] = datetime();
                Db::name(self::$t_name)->where(self::$t_pk, $comment_id)->update($update);
            }
        }

        return $comment;
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
        $param['create_time'] = datetime();
        $comment_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($comment_id)) {
            exception();
        }

        $param[self::$t_pk] = $comment_id;

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
        $comment_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $param['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $comment_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        CommentCache::del($comment_id);

        $param[self::$t_pk] = $comment_id;

        return $param;
    }

    /**
     * 留言删除
     * 
     * @param array $ids 留言列表id
     * 
     * @return array|Exception
     */
    public static function dele($ids)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
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
     * @param array $ids 留言列表id
     * 
     * @return array
     */
    public static function isread($ids)
    {
        $update['is_unread'] = 0;
        $update['read_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->where('is_unread', '=', 1)
            ->update($update);
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
     * @param array $ids 留言列表id
     * 
     * @return array|Exception
     */
    public static function recoverReco($ids)
    {
        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
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
     * @param array $ids 留言列表id
     * 
     * @return array|Exception
     */
    public static function recoverDele($ids)
    {
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->delete();
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
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key   = 'field';
        $field = CommentCache::get($key);
        if (empty($field)) {
            $sql = Db::name(self::$t_name)
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql   = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            CommentCache::set($key, $field);
        }

        return $field;
    }

    /**
     * 表字段是否存在
     * 
     * @param string $field 要检查的字段
     * 
     * @return bool
     */
    public static function tableFieldExist($field)
    {
        $fields = self::tableField();

        foreach ($fields as $v) {
            if ($v == $field) {
                return true;
            }
        }

        return false;
    }
}
