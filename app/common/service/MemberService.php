<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理
namespace app\common\service;

use think\facade\Db;
use app\common\cache\MemberCache;
use app\common\utils\DatetimeUtils;
use app\common\service\file\FileService;

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
            $field = 'member_id,username,nickname,phone,email,avatar_id,sort,remark,create_time,is_disable';
        }

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
            $list[$k]['avatar_url'] = '';
            if (isset($v['avatar_id'])) {
                $list[$k]['avatar_url'] = FileService::fileUrl($v['avatar_id']);
            }
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
            $member['avatar_url'] = FileService::fileUrl($member['avatar_id']);

            $member_wechat = Db::name('member_wechat')
                ->where('member_id', $member_id)
                ->find();
            if ($member_wechat) {
                if (empty($member['nickname'])) {
                    $member['nickname'] = $member_wechat['nickname'];
                }
                if (empty($member['avatar_url'])) {
                    $member['avatar_url'] = $member_wechat['headimgurl'];
                }
                $member_wechat['privilege'] = unserialize($member_wechat['privilege']);
                $member['wechat'] = $member_wechat;
            } else {
                $member['wechat'] = [];
            }

            // 0原密码修改密码，1直接设置新密码
            $member['pwd_edit_type'] = 0;
            if (empty($member['password'])) {
                $member['pwd_edit_type'] = 1;
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
            $update['password'] = md5($param['password']);
        } else {
            $update['password'] = md5($param['password_new']);
        }

        $update['update_time'] = datetime();

        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['member_id'] = $member_id;
        $update['password']  = '';

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
     * 会员统计（数量）
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
     * 会员统计（日期）
     *
     * @param array $date 日期范围
     * 
     * @return array
     */
    public static function statDate($date = [])
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }
        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'date:' . $sta_date . '-' . $end_date;
        $data = MemberCache::get($key);
        if (empty($data)) {
            $data['date'] = $date;
            $dates = DatetimeUtils::betweenDates($sta_date, $end_date);
            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            // 新增会员
            $new = Db::name('member')
                ->field("count(create_time) as num, date_format(login_time,'%Y-%m-%d') as date")
                ->where('create_time', '>=', $sta_time)
                ->where('create_time', '<=', $end_time)
                ->group("date_format(login_time,'%Y-%m-%d')")
                ->select()
                ->toArray();
            $new_x = $new_s = [];
            foreach ($dates as $k => $v) {
                $new_x[$k] = $v;
                $new_s[$k] = 0;
                foreach ($new as $kn => $vn) {
                    if ($v == $vn['date']) {
                        $new_s[$k] = $vn['num'];
                    }
                }
            }
            $data['new'] = ['x' => $new_x, 's' => $new_s];

            // 活跃会员
            $act = Db::name('member')
                ->field("count(login_time) as num, date_format(login_time,'%Y-%m-%d') as date")
                ->where('login_time', '>=', $sta_time)
                ->where('login_time', '<=', $end_time)
                ->group("date_format(login_time,'%Y-%m-%d')")
                ->select()
                ->toArray();
            $act_x = $act_s = [];
            foreach ($dates as $k => $v) {
                $act_x[$k] = $v;
                $act_s[$k] = 0;
                foreach ($act as $ka => $va) {
                    if ($v == $va['date']) {
                        $act_s[$k] = $va['num'];
                    }
                }
            }
            $data['act'] = ['x' => $act_x, 's' => $act_s];

            // 会员总数
            $count_x = $count_s = [];
            foreach ($dates as $k => $v) {
                $count_t = DatetimeUtils::dateEndTime($v);
                $count_x[] = $v;
                $count_s[] = Db::name('member')
                    ->where('is_delete', 0)
                    ->where('create_time', '<=', $count_t)
                    ->count('member_id');
            }
            $data['count'] = ['x' => $count_x, 's' => $count_s];

            MemberCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员统计（总数）
     *
     * @return integer
     */
    public static function statCount()
    {
        $x = $s = [];
        $months = DatetimeUtils::months();
        foreach ($months as $k => $v) {
            $time = [];
            $time = DatetimeUtils::monthStartEnd($v);
            $time[1] = DatetimeUtils::dateEndTime($time[1]);

            $x[] = $v;
            $s[] = Db::name('member')
                ->where('is_delete', 0)
                ->where('create_time', '<=', $time[1])
                ->count('member_id');
        }

        $data['x'] = $x;
        $data['s'] = $s;

        return $data;
    }
}
