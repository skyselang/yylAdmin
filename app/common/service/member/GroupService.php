<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use app\common\cache\member\GroupCache;
use app\common\cache\member\MemberCache;
use app\common\model\member\GroupModel;
use app\common\model\member\AttributesModel;

/**
 * 会员分组
 */
class GroupService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'group_id/d'   => '',
        'group_name/s' => '',
        'group_desc/s' => '',
        'remark/s'     => '',
        'sort/d'       => 250,
        'api_ids/a'    => [],
    ];

    /**
     * 会员分组列表
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
        $model = new GroupModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',group_name,group_desc,remark,sort,is_default,is_disable,create_time,update_time';
        } else {
            $field = $group . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $model = $model->alias('a');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'api_ids' && is_array($wv[2])) {
                $model = $model->join('member_group_apis g', 'a.group_id=g.group_id')->where('g.api_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $append = [];
        if (strpos($field, 'is_default')) {
            $append[] = 'is_default_name';
        }
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
     * 会员分组信息
     *
     * @param int  $id   分组id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = GroupCache::get($id);
        if (empty($info)) {
            $model = new GroupModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('会员分组不存在：' . $id);
                }
                return [];
            }
            $info = $info->append(['api_ids'])->hidden(['apis'])->toArray();

            GroupCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员分组添加
     *
     * @param array $param 分组信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加接口
            if (isset($param['api_ids'])) {
                $model->apis()->saveAll($param['api_ids']);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 会员分组修改
     *
     * @param int|array $ids   分组id
     * @param array     $param 分组信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['api_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改接口
                    if (isset($param['api_ids'])) {
                        $info = $info->append(['api_ids']);
                        relation_update($info, $info['api_ids'], $param['api_ids'], 'apis');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        GroupCache::del($ids);

        return $param;
    }

    /**
     * 会员分组删除
     *
     * @param array $ids  分组id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除接口
                    $info->apis()->detach();
                }
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        GroupCache::del($ids);

        return $update;
    }

    /**
     * 会员分组会员列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function member($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return MemberService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 会员分组会员解除
     *
     * @param array $group_id   分组id
     * @param array $member_ids 会员id
     *
     * @return int
     */
    public static function memberRemove($group_id, $member_ids = [])
    {
        $where[] = ['group_id', 'in', $group_id];
        if (empty($member_ids)) {
            $member_ids = AttributesModel::where($where)->column('member_id');
        }
        $where[] = ['member_id', 'in', $member_ids];

        $res = AttributesModel::where($where)->delete();

        MemberCache::del($member_ids);

        return $res;
    }

    /**
     * 会员分组接口id
     *
     * @param int|array $group_ids 分组id
     * @param array     $where     分组条件
     *
     * @return array 接口id
     */
    public static function api_ids($group_ids, $where = [])
    {
        if (empty($group_ids)) {
            return [];
        }

        if (is_numeric($group_ids)) {
            $group_ids = [$group_ids];
        }

        $where[] = ['group_id', 'in', $group_ids];

        $model = new GroupModel();
        $pk = $model->getPk();
        $group = $model->field($pk)->where($where)->append(['api_ids'])->select()->toArray();

        $api_ids = [];
        foreach ($group as $v) {
            $api_ids = array_merge($api_ids, $v['api_ids']);
        }

        return $api_ids;
    }

    /**
     * 会员分组默认分组id
     *
     * @return array
     */
    public static function default_ids()
    {
        return GroupModel::where(where_delete(['is_default', '=', 1]))->column('group_id');
    }
}
