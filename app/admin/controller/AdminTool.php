<?php
/*
 * @Description  : 实用工具
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-29
 */

namespace app\admin\controller;

use app\admin\service\AdminToolService;
use think\facade\Request;

class AdminTool
{
    /**
     * 生成随机字符串密码
     *
     * @method POST
     * @return json
     */
    public function randomStr()
    {
        $random_len = Request::param('random_len/d', 1);
        $random_ids = Request::param('random_ids/a', []);

        if (empty($random_ids)) {
            error('请选择所用字符');
        }

        $data = AdminToolService::randomStr($random_ids, $random_len);

        return success($data);
    }

    /**
     * 时间戳转换
     *
     * @method POST
     * @return json
     */
    public function timestamp()
    {
        $param = Request::only([
            'from_datetime' => '',
            'to_timestamp' => '',
            'from_timestamp' => '',
            'to_datetime' => '',
        ]);

        if ($param['from_timestamp'] && !is_numeric($param['from_timestamp'])) {
            error('请输入正确的时间戳');
        }

        $data = AdminToolService::timestamp($param);

        return success($data);
    }

    /**
     * MD5加密
     *
     * @method POST
     * @return json
     */
    public function md5Enc()
    {
        $str = Request::param('str', '');

        if (empty($str)) {
            error('请输入字符串');
        }

        $data = AdminToolService::md5Enc($str);

        return success($data);
    }

    /**
     * 生成二维码
     *
     * @method POST
     * @return json
     */
    public function qrcode()
    {
        $qrcode_str = Request::param('qrcode_str', '');

        if (empty($qrcode_str)) {
            error('请输入文本');
        }

        $data = AdminToolService::qrcode($qrcode_str);

        return success($data);
    }
}
