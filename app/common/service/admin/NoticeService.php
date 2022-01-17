<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 公告管理
namespace app\common\service\admin;

use app\common\cache\admin\NoticeCache;
use app\common\model\admin\NoticeModel;
use app\common\model\admin\UserModel;

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

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        if (empty($field)) {
            $field = $pk . ',' . $UserPk . ',title,color,sort,is_open,open_time_start,open_time_end,create_time';
        }

        if (empty($order)) {
            $order = ['is_open' => 'desc', 'sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        foreach ($list as $k => $v) {
            if (isset($v[$UserPk])) {
                $user = UserService::info($v[$UserPk]);
                $list[$k]['username'] = $user['username'];
            }
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 公告信息
     *
     * @param int $id 公告id
     * 
     * @return array
     */
    public static function info($id)
    {
        $info = NoticeCache::get($id);
        if (empty($info)) {
            $model = new NoticeModel();
            $info = $model->find($id);
            if (empty($info)) {
                exception('公告不存在：' . $id);
            }
            $info = $info->toArray();

            $UserModel = new UserModel();
            $UserPk = $UserModel->getPk();
            $user = UserService::info($info[$UserPk]);
            $info['username'] = $user['username'];

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

        $param['create_time'] = datetime();

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
     * @param array $param 公告信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        NoticeCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 公告删除
     *
     * @param array $ids 公告id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            NoticeCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 公告是否开启
     *
     * @param array $ids     公告id
     * @param int   $is_open 是否开启
     * 
     * @return array
     */
    public static function is_open($ids, $is_open)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        $update['is_open']     = $is_open;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            NoticeCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 公告开启时间
     *
     * @param array $ids   公告id
     * @param array $param 开始时间、结束时间
     * 
     * @return array
     */
    public static function opentime($ids, $param)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        $update['open_time_start'] = $param['open_time_start'];
        $update['open_time_end']   = $param['open_time_end'];
        $update['update_time']     = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            NoticeCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}
