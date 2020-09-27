<?php
/*
 * @Description  : 实用工具
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-27
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
        $ids = Request::param('ids/a', []);
        $len = Request::param('len/d', 0);

        $param['strrand_ids'] = $ids;
        $param['strrand_len'] = $len;

        validate(AdminToolValidate::class)->scene('strrand')->check($param);

        $data = AdminToolService::strRand($ids, $len);

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

        $param['strtran_str'] = $str;

        validate(AdminToolValidate::class)->scene('strtran')->check($param);

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
        $param = Request::only(
            [
                'type'      => '',
                'value'     => '',
                'timestamp' => '',
                'datetime'  => '',
            ]
        );

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

        $param['qrcode_str'] = $str;

        validate(AdminToolValidate::class)->scene('qrcode')->check($param);

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
        $param = Request::only(
            [
                'type'  => 'b',
                'value' => 0,
            ],
        );

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
        $param = Request::only(
            [
                'ip'  => '',
            ],
        );

        if (empty($param['ip'])) {
            $param['ip'] = Request::ip();
        }

        $data = AdminToolService::ipQuery($param);

        return success($data);
    }
}
