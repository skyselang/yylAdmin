<?php
/*
 * @Description  : 会员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2020-12-10
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
     * @param string  $field   字段
     * @param integer $page    页数
     * @param integer $limit   数量
     * @param array   $order   排序
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [])
    {
        if (empty($field)) {
            $field = 'member_id,username,nickname,phone,email,sort,remark,create_time,login_time,is_disable';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'member_id' => 'desc'];
        }

        $where[] = ['is_delete', '=', 0];

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
    public static function add($param)
    {
        $param['is_disable']  = '0';
        $param['password']    = md5($param['password']);
        $param['create_time'] = date('Y-m-d H:i:s');

        $member_id = Db::name('member')
            ->insertGetId($param);

        if (empty($member_id)) {
            exception();
        }

        $param['member_id'] = $member_id;

        return $param;
    }

    /**
     * 会员修改
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $member_id = $param['member_id'];

        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('member')
            ->where('member_id', $member_id)
            ->update($param);

        if (empty($update)) {
            exception();
        }

        MemberCache::upd($member_id);

        return $param;
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
                return $member_id . '/avatar';
            });

        $update['avatar']      = 'storage/' . $avatar_name . '?t=' . date('YmdHis');
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $member = MemberCache::upd($member_id);

        return $member;
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
        $data['is_delete']   = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');

        $update = Db::name('member')
            ->where('member_id', $member_id)
            ->update($data);

        if (empty($update)) {
            exception();
        }

        $data['member_id'] = $member_id;

        MemberCache::upd($member_id);

        return $data;
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
        $password  = $param['password'];

        $data['password']    = md5($password);
        $data['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($data);

        if (empty($res)) {
            exception();
        }

        MemberCache::upd($member_id);

        return $param;
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
        $password  = $param['password_new'];

        $data['password']    = md5($password);
        $data['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($data);

        if (empty($res)) {
            exception();
        }

        MemberCache::upd($member_id);

        return $param;
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

        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('member')
            ->where('member_id', $member_id)
            ->update($param);

        if (empty($update)) {
            exception();
        }

        MemberCache::upd($member_id);

        return $param;
    }

    /**
     * 会员模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'username|nickname|phone|email')
    {
        $member = Db::name('member')
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
    public static function etQuery($keyword, $field = 'username|nickname|phone|email')
    {
        $member = Db::name('member')
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $member;
    }
}
