<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\AccordCache;
use app\common\model\setting\AccordModel;

/**
 * 协议管理
 */
class AccordService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'accord_id/d' => '',
        'unique/s'    => '',
        'name/s'      => '',
        'desc/s'      => '',
        'content/s'   => '',
        'remark/s'    => '',
        'sort/d'      => 250,
    ];

    /**
     * 协议列表
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
        $model = new AccordModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',unique,name,desc,remark,sort,is_disable,create_time,update_time';
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
     * 协议信息
     * 
     * @param int|string $id   协议id、标识
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = AccordCache::get($id);
        if (empty($info)) {
            $model = new AccordModel();
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
                    exception('协议不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            AccordCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 协议添加
     *
     * @param array $param 协议信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new AccordModel();
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
     * 协议修改
     *     
     * @param int|array $ids   协议id
     * @param array     $param 协议信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new AccordModel();
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

        AccordCache::del($ids);
        AccordCache::del($unique);

        return $param;
    }

    /**
     * 协议删除
     * 
     * @param array $ids  协议id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new AccordModel();
        $pk = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

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

        AccordCache::del($ids);
        AccordCache::del($unique);

        return $update;
    }
}
