<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-03-20
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\UserCache;
use app\common\utils\Datetime;
use app\admin\service\TokenService;

class UserService
{
    /**
     * 用户列表
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
            $field = 'user_id,username,nickname,phone,email,sort,remark,create_time,login_time,is_disable';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['sort' => 'desc', 'user_id' => 'desc'];
        }

        $count = Db::name('user')
            ->where($where)
            ->count('user_id');

        $list = Db::name('user')
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
     * 用户信息
     *
     * @param integer $user_id 用户id
     * 
     * @return array
     */
    public static function info($user_id)
    {
        $user = UserCache::get($user_id);

        if (empty($user)) {
            $user = Db::name('user')
                ->where('user_id', $user_id)
                ->find();

            if (empty($user)) {
                exception('用户不存在：' . $user_id);
            }

            $user['avatar']     = file_url($user['avatar']);
            $user['user_token'] = TokenService::create($user);

            UserCache::set($user_id, $user);
        }

        return $user;
    }

    /**
     * 用户添加
     *
     * @param array $param 用户信息
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
            $param['create_time'] = datetime();

            $user_id = Db::name('user')
                ->insertGetId($param);

            if (empty($user_id)) {
                exception();
            }

            $param['user_id'] = $user_id;

            unset($param['password']);

            return $param;
        }
    }

    /**
     * 用户修改
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $user_id = $param['user_id'];

        if ($method == 'get') {
            $data['user_info']   = self::info($user_id);
            $data['region_tree'] = RegionService::info('tree');

            unset($data['user_info']['password'], $data['user_info']['user_token']);

            return $data;
        } else {
            unset($param['user_id']);

            $param['update_time'] = datetime();

            $res = Db::name('user')
                ->where('user_id', $user_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['user_id'] = $user_id;

            UserCache::upd($user_id);

            return $param;
        }
    }

    /**
     * 用户修改头像
     *
     * @param array $param 头像信息
     * 
     * @return array
     */
    public static function avatar($param)
    {
        $user_id = $param['user_id'];
        $avatar  = $param['avatar'];

        $avatar_name = Filesystem::disk('public')
            ->putFile('user', $avatar, function () use ($user_id) {
                return $user_id . '/' . $user_id . '_avatar';
            });

        $update['avatar']      = 'storage/' . $avatar_name . '?t=' . date('YmdHis');
        $update['update_time'] = datetime();

        $res = Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['avatar'] = file_url($update['avatar']);

        UserCache::upd($user_id);

        return $update;
    }

    /**
     * 用户删除
     *
     * @param integer $user_id 用户id
     * 
     * @return array
     */
    public static function dele($user_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['user_id'] = $user_id;

        UserCache::upd($user_id);

        return $update;
    }

    /**
     * 用户密码重置
     *
     * @param array $param 密码信息
     * 
     * @return array
     */
    public static function password($param)
    {
        $user_id = $param['user_id'];

        $update['password']    = md5($param['password']);
        $update['update_time'] = datetime();

        $res = Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['user_id'] = $user_id;
        $update['password']  = $res;

        UserCache::upd($user_id);

        return $update;
    }

    /**
     * 用户修改密码
     *
     * @param array $param 密码信息
     * 
     * @return array
     */
    public static function pwdedit($param)
    {
        $user_id = $param['user_id'];

        $update['password']    = md5($param['password_new']);
        $update['update_time'] = datetime();

        $res = Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['user_id']  = $user_id;
        $update['password'] = $res;

        UserCache::upd($user_id);

        return $update;
    }

    /**
     * 用户是否禁用
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function disable($param)
    {
        $user_id = $param['user_id'];

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = datetime();

        $res = Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['user_id'] = $user_id;

        UserCache::upd($user_id);

        return $update;
    }

    /**
     * 用户模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'username|nickname|email|phone')
    {
        $user = Db::name('user')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $user;
    }

    /**
     * 用户精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'username|nickname|email|phone')
    {
        $user = Db::name('user')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $user;
    }

    /**
     * 用户数量统计
     *
     * @param string $date 日期
     * @param string $type 类型：new新增，act活跃
     *
     * @return integer
     */
    public static function staNumber($date = 'total', $type = 'new')
    {
        $key  = $date . ':' . $type;
        $data = UserCache::get($key);

        if (empty($data)) {
            $where[] = ['is_delete', '=', 0];

            if ($date == 'total') {
                $where[] = ['user_id', '>', 0];
            } else {
                if ($date == 'yesterday') {
                    $yesterday = Datetime::yesterday();
                    list($sta_time, $end_time) = Datetime::datetime($yesterday);
                } elseif ($date == 'thisweek') {
                    list($start, $end) = Datetime::thisWeek();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastweek') {
                    list($start, $end) = Datetime::lastWeek();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'thismonth') {
                    list($start, $end) = Datetime::thisMonth();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastmonth') {
                    list($start, $end) = Datetime::lastMonth();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } else {
                    $today = Datetime::today();
                    list($sta_time, $end_time) = Datetime::datetime($today);
                }

                if ($type == 'act') {
                    $where[] = ['login_time', '>=', $sta_time];
                    $where[] = ['login_time', '<=', $end_time];
                } else {
                    $where[] = ['create_time', '>=', $sta_time];
                    $where[] = ['create_time', '<=', $end_time];
                }
            }

            $data = Db::name('user')
                ->field('user_id')
                ->where($where)
                ->count('user_id');

            UserCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 用户日期统计
     *
     * @param array  $date 日期范围
     * @param string $type 类型：new新增，act活跃
     * 
     * @return array
     */
    public static function staDate($date = [], $type = 'new')
    {
        if (empty($date)) {
            $date[0] = Datetime::daysAgo(30);
            $date[1] = Datetime::today();
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'date:' . $sta_date . '-' . $end_date . ':' . $type;
        $data = UserCache::get($key);

        if (empty($data)) {
            $sta_time = Datetime::dateStartTime($sta_date);
            $end_time = Datetime::dateEndTime($end_date);

            if ($type == 'act') {
                $field = "count(login_time) as num, date_format(login_time,'%Y-%m-%d') as date";
                $where[] = ['login_time', '>=', $sta_time];
                $where[] = ['login_time', '<=', $end_time];
                $group = "date_format(login_time,'%Y-%m-%d')";
            } else {
                $field = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
                $where[] = ['create_time', '>=', $sta_time];
                $where[] = ['create_time', '<=', $end_time];
                $group = "date_format(create_time,'%Y-%m-%d')";
            }

            $x_data = Datetime::betweenDates($sta_date, $end_date);
            $y_data = [];

            $user = Db::name('user')
                ->field($field)
                ->where($where)
                ->group($group)
                ->select();

            foreach ($x_data as $k => $v) {
                $y_data[$k] = 0;
                foreach ($user as $ku => $vu) {
                    if ($v == $vu['date']) {
                        $y_data[$k] = $vu['num'];
                    }
                }
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['date']   = $date;

            UserCache::set($key, $data);
        }

        return $data;
    }
}
