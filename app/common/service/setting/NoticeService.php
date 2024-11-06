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
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'notice_id/d'   => '',
        'image_id/d'    => 0,
        'type/d'        => 0,
        'title/s'       => '',
        'title_color/s' => '#606266',
        'start_time/s'  => '',
        'end_time/s'    => '',
        'desc/s'        => '',
        'content/s'     => '',
        'remark/s'      => '',
        'sort/d'        => 250,
    ];

    /**
     * 通告列表
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
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',image_id,type,title,title_color,start_time,end_time,is_disable,remark,sort,create_time,update_time';
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
        if (strpos($field, 'type')) {
            $append[] = 'type_name';
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

        $types = SettingService::noticeTypes();

        return compact('count', 'pages', 'page', 'limit', 'list', 'types');
    }

    /**
     * 通告信息
     *
     * @param int  $id   通告id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = NoticeCache::get($id);
        if (empty($info)) {
            $model = new NoticeModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('通告不存在：' . $id);
                }
                return [];
            }
            $info = $info->append(['image_url', 'type_name'])->hidden(['image'])->toArray();

            NoticeCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 通告添加
     *
     * @param array $param 通告信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new NoticeModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
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
     * @return array|Exception
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
     * @return array|Exception
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
