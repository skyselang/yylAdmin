<?php
/*
 * @Description  : 会员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-05-14
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\MemberCache;
use app\common\utils\DatetimeUtils;

class MemberService
{
    /**
     * 会员列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'member_id,username,nickname,phone,email,avatar,sort,remark,create_time,login_time,is_disable';
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

        foreach ($list as $k => $v) {
            $list[$k]['avatar'] = file_url($v['avatar']);
        }

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

            $member_wechat = Db::name('member_wechat')
                ->where('member_id', $member_id)
                ->find();

            if ($member_wechat) {
                if (empty($member['nickname'])) {
                    $member['nickname'] = $member_wechat['nickname'];
                }
                if (empty($member['avatar'])) {
                    $member['avatar'] = $member_wechat['headimgurl'];
                }
                $member_wechat['privilege'] = unserialize($member_wechat['privilege']);
                $member['wechat'] = $member_wechat;
            } else {
                $member['wechat'] = [];
            }

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
        $param['password']    = md5($param['password']);
        $param['create_time'] = datetime();

        $member_id = Db::name('member')
            ->insertGetId($param);

        if (empty($member_id)) {
            exception();
        }

        $param['member_id'] = $member_id;

        unset($param['password']);

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

        unset($param['member_id']);

        $param['update_time'] = datetime();

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
        $update['delete_time'] = datetime();

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['member_id'] = $member_id;

        MemberCache::del($member_id);

        return $update;
    }

    /**
     * 会员更换头像
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
        $update['update_time'] = datetime();

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
     * 会员修改密码
     *
     * @param array $param 密码信息
     * 
     * @return array
     */
    public static function pwd($param)
    {
        $member_id = $param['member_id'];

        if (isset($param['password'])) {
            $update['password']    = md5($param['password']);
        } else {
            $update['password']    = md5($param['password_new']);
        }

        $update['update_time'] = datetime();

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
        $update['update_time'] = datetime();

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
    public static function likeQuery($keyword, $field = 'username|nickname|phone|email')
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
    public static function equQuery($keyword, $field = 'username|nickname|phone|email')
    {
        $member = Db::name('member')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $member;
    }

    /**
     * 会员数量统计
     *
     * @param string $date 日期
     * @param string $type 类型：new新增，act活跃
     *
     * @return integer
     */
    public static function statNum($date = 'total', $type = 'new')
    {
        $key  = $date . ':' . $type;
        $data = MemberCache::get($key);

        if (empty($data)) {
            $where[] = ['is_delete', '=', 0];

            if ($date == 'total') {
                $where[] = ['member_id', '>', 0];
            } else {
                if ($date == 'yesterday') {
                    $yesterday = DatetimeUtils::yesterday();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($yesterday);
                } elseif ($date == 'thisweek') {
                    list($start, $end) = DatetimeUtils::thisWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastweek') {
                    list($start, $end) = DatetimeUtils::lastWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'thismonth') {
                    list($start, $end) = DatetimeUtils::thisMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastmonth') {
                    list($start, $end) = DatetimeUtils::lastMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } else {
                    $today = DatetimeUtils::today();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($today);
                }

                if ($type == 'act') {
                    $where[] = ['login_time', '>=', $sta_time];
                    $where[] = ['login_time', '<=', $end_time];
                } else {
                    $where[] = ['create_time', '>=', $sta_time];
                    $where[] = ['create_time', '<=', $end_time];
                }
            }

            $data = Db::name('member')
                ->field('member_id')
                ->where($where)
                ->count('member_id');

            MemberCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员日期统计
     *
     * @param array  $date 日期范围
     * @param string $type 类型：new新增，act活跃
     * 
     * @return array
     */
    public static function statDate($date = [], $type = 'new')
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'date:' . $sta_date . '-' . $end_date . ':' . $type;
        $data = MemberCache::get($key);

        if (empty($data)) {
            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            if ($type == 'act') {
                $field   = "count(login_time) as num, date_format(login_time,'%Y-%m-%d') as date";
                $where[] = ['login_time', '>=', $sta_time];
                $where[] = ['login_time', '<=', $end_time];
                $group   = "date_format(login_time,'%Y-%m-%d')";
            } else {
                $field   = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
                $where[] = ['create_time', '>=', $sta_time];
                $where[] = ['create_time', '<=', $end_time];
                $group   = "date_format(create_time,'%Y-%m-%d')";
            }

            $x_data = DatetimeUtils::betweenDates($sta_date, $end_date);
            $y_data = [];

            $member = Db::name('member')
                ->field($field)
                ->where($where)
                ->group($group)
                ->select();

            foreach ($x_data as $k => $v) {
                $y_data[$k] = 0;
                foreach ($member as $ku => $vu) {
                    if ($v == $vu['date']) {
                        $y_data[$k] = $vu['num'];
                    }
                }
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['date']   = $date;

            MemberCache::set($key, $data);
        }

        return $data;
    }
}
