<?php
/*
 * @Description  : 实用工具
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-08-15
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminToolService;
use app\admin\validate\AdminToolValidate;

class AdminTool
{
    /**
     * 字符串
     *
     * @method GET
     *
     * @return json
     */
    public function string()
    {
        $str = Request::param('str/s', '');

        $param['string_str'] = $str;
        validate(AdminToolValidate::class)->scene('string')->check($param);

        $data = AdminToolService::string($str);

        return success($data);
    }

    /**
     * 随机字符串
     *
     * @method POST
     * 
     * @return json
     */
    public function strran()
    {
        $ids = Request::param('ids/a', []);
        $len = Request::param('len/d', 0);

        $param['strran_ids'] = $ids;
        $param['strran_len'] = $len;
        validate(AdminToolValidate::class)->scene('strran')->check($param);

        $data = AdminToolService::strran($ids, $len);

        return success($data);
    }

    /**
     * 时间戳转换
     *
     * @method POST
     * 
     * @return json
     */
    public function timestamp()
    {
        $param = Request::only(
            [
                'trantype'  => 'time',
                'datetime'  => '',
                'timestamp' => '',
            ]
        );

        $param['timestamp_trantype']  = $param['trantype'];
        $param['timestamp_datetime']  = $param['datetime'];
        $param['timestamp_timestamp'] = $param['timestamp'];

        validate(AdminToolValidate::class)->scene('timestamp_type')->check($param);
        if ($param['trantype'] == 'time') {
            validate(AdminToolValidate::class)->scene('timestamp_time')->check($param);
        }
        if ($param['trantype'] == 'date') {
            validate(AdminToolValidate::class)->scene('timestamp_date')->check($param);
        }

        $data = AdminToolService::timestamp($param);

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
        validate(AdminToolValidate::class)->scene('timestamp_type')->check($param);

        $data = AdminToolService::qrcode($str);

        return success($data);
    }
}
