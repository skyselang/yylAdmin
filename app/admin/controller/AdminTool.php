<?php
/*
 * @Description  : 实用工具合集
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminToolValidate;
use app\admin\service\AdminToolService;

class AdminTool
{
    /**
     * 随机字符串
     *
     * @method POST
     * 
     * @return json
     */
    public function strRand()
    {
        $param['strrand_ids'] = Request::param('ids/a', [1, 2, 3]);
        $param['strrand_len'] = Request::param('len/d', 12);

        validate(AdminToolValidate::class)->scene('strrand')->check($param);

        $data = AdminToolService::strRand($param);

        return success($data);
    }

    /**
     * 字符串转换
     *
     * @method POST
     *
     * @return json
     */
    public function strTran()
    {
        $str = Request::param('str/s', '');

        $data = AdminToolService::strTran($str);

        return success($data);
    }

    /**
     * 时间戳转换
     *
     * @method POST
     * 
     * @return json
     */
    public function timeTran()
    {
        $param['type']      = Request::param('type', '');
        $param['value']     = Request::param('value', '');
        $param['timestamp'] = Request::param('timestamp', '');
        $param['datetime']  = Request::param('datetime', '');

        $data = AdminToolService::timeTran($param);

        return success($data);
    }

    /**
     * 生成二维码
     *
     * @method POST
     * 
     * @return json
     */
    public function qrcode()
    {
        $str = Request::param('str/s', '');

        $data = AdminToolService::qrcode($str);

        return success($data);
    }

    /**
     * 字节转换
     *
     * @method POST
     *
     * @return json
     */
    public function byteTran()
    {
        $param['type']  = Request::param('type', 'B');
        $param['value'] = Request::param('value', 1024);

        $data = AdminToolService::byteTran($param);

        return success($data);
    }

    /**
     * IP查询
     *
     * @method POST
     *
     * @return json
     */
    public function ipQuery()
    {
        $ip = Request::param('ip/s', '');

        if (empty($ip)) {
            $ip = Request::ip();
        }

        $data = AdminToolService::ipQuery($ip);

        return success($data);
    }
}
