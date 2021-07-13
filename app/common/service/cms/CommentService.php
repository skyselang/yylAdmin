<?php
/*
 * @Description  : 留言管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-13
 */

namespace app\common\service\cms;

use think\facade\Db;
use app\common\cache\cms\CommentCache;

class CommentService
{
    // 留言表名
    protected static $db_name = 'cms_comment';

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
            $field = 'comment_id,call,mobile,tel,title,remark,is_read,create_time,update_time,delete_time';
        }

        if (empty($order)) {
            $order = ['comment_id' => 'desc'];
        }

        $count = Db::name(self::$db_name)
            ->where($where)
            ->count('comment_id');

        $list = Db::name(self::$db_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
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
            $comment = Db::name(self::$db_name)
                ->where('comment_id', $comment_id)
                ->find();
            if (empty($comment)) {
                exception('留言不存在：' . $comment_id);
            }
            if (empty($comment['is_read'])) {
                $update['is_read']   = 1;
                $update['read_time'] = $comment['read_time'] = datetime();
                Db::name(self::$db_name)->where('comment_id', $comment_id)->update($update);
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
        $comment_id = Db::name(self::$db_name)
            ->insertGetId($param);
        if (empty($comment_id)) {
            exception();
        }

        $param['comment_id'] = $comment_id;

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
        $comment_id = $param['comment_id'];

        unset($param['comment_id']);

        $param['update_time'] = datetime();
        $res = Db::name(self::$db_name)
            ->where('comment_id', $comment_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param['comment_id'] = $comment_id;

        return $param;
    }

    /**
     * 留言删除
     * 
     * @param array $comment 留言列表
     * 
     * @return array|Exception
     */
    public static function dele($comment)
    {
        $comment_ids = array_column($comment, 'comment_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('comment_id', 'in', $comment_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['comment_ids'] = $comment_ids;

        return $update;
    }

    /**
     * 留言已读
     *
     * @param array $comment 留言列表
     * 
     * @return array
     */
    public static function isread($comment)
    {
        $comment_ids = array_column($comment, 'comment_id');

        $update['is_read']   = 1;
        $update['read_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('comment_id', 'in', $comment_ids)
            ->where('is_read', '=', 0)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['comment_ids'] = $comment_ids;

        return $update;
    }

    /**
     * 留言回收站恢复
     * 
     * @param array $comment 留言列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($comment)
    {
        $comment_ids = array_column($comment, 'comment_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('comment_id', 'in', $comment_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['comment_ids'] = $comment_ids;

        return $update;
    }

    /**
     * 留言回收站删除
     * 
     * @param array $comment 留言列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($comment)
    {
        $comment_ids = array_column($comment, 'comment_id');

        $res = Db::name(self::$db_name)
            ->where('comment_id', 'in', $comment_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        $update['comment_ids'] = $comment_ids;

        return $update;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = CommentCache::get($key);
        if (empty($field)) {
            $sql = Db::name(self::$db_name)
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
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

        foreach ($fields as $k => $v) {
            if ($v == $field) {
                return true;
            }
        }

        return false;
    }
}
