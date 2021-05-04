<?php
/*
 * @Description  : 实用工具
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-04-30
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminUtilsValidate;
use app\common\service\AdminUtilsService;
use app\common\service\AdminUserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("实用工具")
 * @Apidoc\Group("admin")
 */
class AdminUtils
{
    /**
     * @Apidoc\Title("接口文档")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     */
    public function apidoc()
    {
        $admin_user      = AdminUserService::info(admin_user_id());
        $admin_token     = $admin_user['admin_token'];
        $admin_token_sub = substr($admin_token, 0, 16) . '...';

        $data['apidoc_url']      = server_url() . '/apidoc/index.html?t=' . time();
        $data['apidoc_pwd']      = config('apidoc.auth.password');
        $data['admin_user_id']   = $admin_user['admin_user_id'];
        $data['admin_token']     = $admin_token;
        $data['admin_token_sub'] = $admin_token_sub;

        return success($data);
    }

    /**
     * @Apidoc\Title("服务器信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     */
    public function server()
    {
        $data = AdminUtilsService::server();

        return success($data);
    }

    /**
     * @Apidoc\Title("随机字符串")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("strrand_ids", type="array", default="[1,2,3]", desc="字符类型")
     * @Apidoc\Param("strrand_len", type="int", default="12", desc="字符长度")
     * @Apidoc\Returned(ref="return")
     */
    public function strrand()
    {
        $param['strrand_ids'] = Request::param('ids/a', [1, 2, 3]);
        $param['strrand_len'] = Request::param('len/d', 12);

        validate(AdminUtilsValidate::class)->scene('strrand')->check($param);

        $data = AdminUtilsService::strrand($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("字符串转换")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("str", type="string", default="", desc="字符串")
     * @Apidoc\Returned(ref="return")
     */
    public function strtran()
    {
        $str = Request::param('str/s', '');

        $data = AdminUtilsService::strtran($str);

        return success($data);
    }

    /**
     * @Apidoc\Title("时间戳转换")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("type", type="string", default="", desc="转换类型")
     * @Apidoc\Param("value", type="string", default="", desc="时间、时间戳")
     * @Apidoc\Returned(ref="return")
     */
    public function timestamp()
    {
        $param['type']  = Request::param('type', '');
        $param['value'] = Request::param('value', '');

        $data = AdminUtilsService::timestamp($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("字节转换")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("type", type="string", default="B", desc="转换类型")
     * @Apidoc\Param("value", type="string", default="1024", desc="数值")
     * @Apidoc\Returned(ref="return")
     */
    public function bytetran()
    {
        $param['type']  = Request::param('type', 'B');
        $param['value'] = Request::param('value', 1024);

        $data = AdminUtilsService::bytetran($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("IP信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("ip", type="string", default="", desc="ip")
     * @Apidoc\Returned(ref="return")
     */
    public function ipinfo()
    {
        $ip = Request::param('ip/s', '');

        if (empty($ip)) {
            $ip = Request::ip();
        }

        $data = AdminUtilsService::ipinfo($ip);

        return success($data);
    }
}
