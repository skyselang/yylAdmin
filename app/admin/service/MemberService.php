<?php
/*
 * @Description  : 会员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2020-12-27
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\MemberCache;
use app\index\service\TokenService;

class MemberService
{
    /**
     * 会员列表
     *
     * @param array   $where   条件
     * @param integer $page    页数
     * @param integer $limit   数量
     * @param array   $order   排序
     * @param string  $field   字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'member_id,username,nickname,phone,email,sort,remark,create_time,login_time,is_disable';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['sort' => 'desc', 'member_id' => 'desc'];
        }

        $count = Db::name('member')
            ->where($where)
            ->count('member_id');

        $list = Db::name('member')
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
     * 会员信息
     *
     * @param integer $member_id 会员id
     * 
     * @return array
     */
    public static function info($member_id)
    {
        $member = MemberCache::get($member_id);

        if (empty($member)) {
            $member = Db::name('member')
                ->where('member_id', $member_id)
                ->find();

            if (empty($member)) {
                exception('会员不存在：' . $member_id);
            }

            $member['avatar'] = file_url($member['avatar']);
            $member['token']  = TokenService::create($member);

            MemberCache::set($member_id, $member);
        }

        return $member;
    }

    /**
     * 会员添加
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function add($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $data['region_tree'] = RegionService::info('tree');

            return $data;
        } else {
            $param['password']    = md5($param['password']);
            $param['create_time'] = date('Y-m-d H:i:s');

            $member_id = Db::name('member')
                ->insertGetId($param);

            if (empty($member_id)) {
                exception();
            }

            $param['member_id'] = $member_id;

            unset($param['password']);

            return $param;
        }
    }

    /**
     * 会员修改
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $member_id = $param['member_id'];

        if ($method == 'get') {
            $data['member_info'] = self::info($member_id);
            $data['region_tree'] = RegionService::info('tree');

            unset($data['member_info']['password'], $data['member_info']['token']);

            return $data;
        } else {
            unset($param['member_id']);

            $param['update_time'] = date('Y-m-d H:i:s');

            $res = Db::name('member')
                ->where('member_id', $member_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['member_id'] = $member_id;

            MemberCache::upd($member_id);

            return $param;
        }
    }

    /**
     * 会员修改头像
     *
     * @param array $param 头像信息
     * 
     * @return array
     */
    public static function avatar($param)
    {
        $member_id = $param['member_id'];
        $avatar    = $param['avatar'];

        $avatar_name = Filesystem::disk('public')
            ->putFile('member', $avatar, function () use ($member_id) {
                return $member_id . '/' . $member_id . '_avatar';
            });

        $update['avatar']      = 'storage/' . $avatar_name . '?t=' . date('YmdHis');
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['avatar'] = file_url($update['avatar']);

        MemberCache::upd($member_id);

        return $update;
    }

    /**
     * 会员删除
     *
     * @param integer $member_id 会员id
     * 
     * @return array
     */
    public static function dele($member_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['member_id'] = $member_id;

        MemberCache::upd($member_id);

        return $update;
    }

    /**
     * 会员密码重置
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function password($param)
    {
        $member_id = $param['member_id'];

        $update['password']    = md5($param['password']);
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['member_id'] = $member_id;
        $update['password']  = $res;

        MemberCache::upd($member_id);

        return $update;
    }

    /**
     * 会员修改密码
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function pwdedit($param)
    {
        $member_id = $param['member_id'];

        $update['password']    = md5($param['password_new']);
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['member_id'] = $member_id;
        $update['password']  = $res;

        MemberCache::upd($member_id);

        return $update;
    }

    /**
     * 会员是否禁用
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function disable($param)
    {
        $member_id = $param['member_id'];

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['member_id'] = $member_id;

        MemberCache::upd($member_id);

        return $update;
    }

    /**
     * 会员模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'username|nickname|email|phone')
    {
        $member = Db::name('member')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $member;
    }

    /**
     * 会员精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'username|nickname|email|phone')
    {
        $member = Db::name('member')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $member;
    }
}
