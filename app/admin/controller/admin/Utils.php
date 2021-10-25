<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 实用工具控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\UtilsValidate;
use app\common\service\admin\UtilsService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("实用工具")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("980")
 */
class Utils
{
    /**
     * @Apidoc\Title("随机字符串")
     * @Apidoc\Param("strrand_ids", type="array", require=true, default="[1,2,3]", desc="字符类型")
     * @Apidoc\Param("strrand_len", type="int", require=true, default="12", desc="字符长度")
     */
    public function strrand()
    {
        $param['strrand_ids'] = Request::param('ids/a', [1, 2, 3]);
        $param['strrand_len'] = Request::param('len/d', 12);

        validate(UtilsValidate::class)->scene('strrand')->check($param);

        $data = UtilsService::strrand($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("字符串转换")
     * @Apidoc\Param("str", type="string", default=" ", desc="字符串")
     */
    public function strtran()
    {
        $str = Request::param('str/s', '');

        $data = UtilsService::strtran($str);

        return success($data);
    }

    /**
     * @Apidoc\Title("时间戳转换")
     * @Apidoc\Param("type", type="string", default=" ", desc="转换类型")
     * @Apidoc\Param("value", type="string", default=" ", desc="时间、时间戳")
     */
    public function timestamp()
    {
        $param['type']  = Request::param('type', '');
        $param['value'] = Request::param('value', '');

        $data = UtilsService::timestamp($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("字节转换")
     * @Apidoc\Param("type", type="string", default="B", desc="转换类型")
     * @Apidoc\Param("value", type="string", default="1024", desc="数值")
     */
    public function bytetran()
    {
        $param['type']  = Request::param('type', 'B');
        $param['value'] = Request::param('value', 1024);

        $data = UtilsService::bytetran($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("IP信息")
     * @Apidoc\Param("ip", type="string", default=" ", desc="ip")
     */
    public function ipinfo()
    {
        $ip = Request::param('ip/s', '');
        if (empty($ip)) {
            $ip = Request::ip();
        }

        $data = UtilsService::ipinfo($ip);

        return success($data);
    }

    /**
     * @Apidoc\Title("服务器信息")
     */
    public function server()
    {
        $data = UtilsService::server();

        return success($data);
    }
}
