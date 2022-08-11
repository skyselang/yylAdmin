<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\admin;

use app\common\cache\admin\NoticeCache;
use app\common\model\admin\NoticeModel;

/**
 * 公告管理
 */
class NoticeService
{
    /**
     * 公告列表
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
        $model = new NoticeModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',admin_user_id,username,title,color,sort,is_open,open_time_start,open_time_end,create_time';
        }
        
        if (empty($order)) {
            $order = ['is_open' => 'desc', 'sort' => 'desc', 'open_time_start' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 公告信息
     *
     * @param int  $id   公告id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = NoticeCache::get($id);
        if (empty($info)) {
            $model = new NoticeModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('公告不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $user = UserService::info($info['admin_user_id'], false);
            $info['username_now'] = $user['username'] ?? '';

            NoticeCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 公告添加
     *
     * @param array $param 公告信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        $user = UserService::info(admin_user_id(), false);

        $param['admin_user_id'] = $user['admin_user_id'] ?? 0;
        $param['username']      = $user['username'] ?? '';
        $param['create_time']   = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 公告修改
     *
     * @param mixed $ids    公告id
     * @param array $update 公告信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        NoticeCache::del($ids);

        return $update;
    }

    /**
     * 公告删除
     *
     * @param array $ids  公告id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new NoticeModel();
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

        $update['ids'] = $ids;

        NoticeCache::del($ids);

        return $update;
    }
}
