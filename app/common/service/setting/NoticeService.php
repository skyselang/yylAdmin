<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\NoticeCache;
use app\common\model\setting\NoticeModel;

/**
 * 通告管理
 */
class NoticeService
{
    /**
     * 通告列表
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
            $field = $pk . ',type,title,title_color,start_time,end_time,is_disable,sort,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->append(['type_name'])
            ->page($page)->limit($limit)->order($order)->select()->toArray();

        $types = SettingService::notice_types();

        return compact('count', 'pages', 'page', 'limit', 'list', 'types');
    }

    /**
     * 通告信息
     *
     * @param int  $id   通告id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = NoticeCache::get($id);
        if (empty($info)) {
            $model = new NoticeModel();

            $info = $model->append(['type_name'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('通告不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            NoticeCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 通告添加
     *
     * @param array $param 通告信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 通告修改
     *
     * @param int|array $ids   通告id
     * @param array     $param 通告信息
     * 
     * @return array
     */
    public static function edit($ids, $param = [])
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        NoticeCache::del($ids);

        return $param;
    }

    /**
     * 通告删除
     *
     * @param array $ids  通告id
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
            $update = delete_update();
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
