<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\CarouselCache;
use app\common\model\setting\CarouselModel;
use app\common\service\file\SettingService;

/**
 * 轮播管理
 */
class CarouselService
{
    /**
     * 轮播列表
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
        $model = new CarouselModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',file_id,file_type,title,link,position,sort,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->with(['file'])
            ->append(['file_url', 'file_type_name'])
            ->hidden(['file'])
            ->page($page)->limit($limit)->order($order)->select()->toArray();

        $filetypes = self::fileTypes();

        return compact('count', 'pages', 'page', 'limit', 'list', 'filetypes');
    }

    /**
     * 轮播信息
     * 
     * @param int  $id   轮播id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = CarouselCache::get($id);
        if (empty($info)) {
            $model = new CarouselModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('轮播不存在：' . $id);
                }
                return [];
            }
            $info = $info->append(['file_url', 'file_name', 'file_ext'])->toArray();

            CarouselCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 轮播添加
     *
     * @param array $param 轮播信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new CarouselModel();
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
     * 轮播修改
     *     
     * @param int|array $ids   轮播id
     * @param array     $param 轮播信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new CarouselModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        CarouselCache::del($ids);

        return $param;
    }

    /**
     * 轮播删除
     * 
     * @param array $ids  轮播id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new CarouselModel();
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

        CarouselCache::del($ids);

        return $update;
    }

    /**
     * 轮播类型数组
     *
     * @return array
     */
    public static function fileTypes()
    {
        return SettingService::fileTypes();
    }
}
