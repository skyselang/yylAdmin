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
     * 协议列表
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
        $model = new AccordModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',unique,name,sort,is_disable,create_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 协议信息
     * 
     * @param string $id   协议id/标识
     * @param bool   $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = AccordCache::get($id);

        if (empty($info)) {
            $model = new AccordModel();
            
            if (is_numeric($id)) {
                $info = $model->find($id);
            } else {
                $info = $model->where('unique', $id)->find();
            }
            
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
