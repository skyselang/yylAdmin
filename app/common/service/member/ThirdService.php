<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use app\common\cache\member\ThirdCache;
use app\common\model\member\ThirdModel;

/**
 * 会员第三方账号
 */
class ThirdService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'third_id/d'    => '',
        'member_id/d'   => '',
        'platform/s'    => '',
        'application/s' => '',
        'unionid/s'     => '',
        'openid/s'      => '',
        'headimgurl/s'  => '',
        'nickname/s'    => '',
        'remark/s'      => '',
    ];

    /**
     * 会员第三方账号列表
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
        $model = new ThirdModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',member_id,platform,application,headimgurl,nickname,is_disable,login_num,login_ip,login_region,login_time,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'member_id') !== false) {
            $with[]   = $hidden[] = 'member';
            $append[] = 'member_nickname';
            $append[] = 'member_username';
        }
        if (strpos($field, 'platform') !== false) {
            $append[] = 'platform_name';
        }
        if (strpos($field, 'application') !== false) {
            $append[] = 'application_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $model->where($where)->count();
        $pages = 0;
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

        $platforms    = SettingService::platforms('', true);
        $applications = SettingService::applications('', true);

        return compact('count', 'pages', 'page', 'limit', 'list', 'platforms', 'applications');
    }

    /**
     * 会员第三方账号信息
     *
     * @param int  $id   第三方账号id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = ThirdCache::get($id);
        if (empty($info)) {
            $model = new ThirdModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('会员第三方账号不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            ThirdCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员第三方账号添加
     *
     * @param array $param 第三方账号信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new ThirdModel();
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
     * 会员第三方账号修改
     *
     * @param int|array $ids   第三方账号id
     * @param array     $param 第三方账号信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new ThirdModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        ThirdCache::del($ids);

        return $param;
    }

    /**
     * 会员第三方账号删除
     *
     * @param array $ids  第三方账号id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new ThirdModel();
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

        ThirdCache::del($ids);

        return $update;
    }
}
