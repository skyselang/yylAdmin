<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\UtilsValidate;
use app\common\service\admin\UtilsService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("实用工具")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("730")
 */
class Utils extends BaseController
{
    /**
     * @Apidoc\Title("随机字符串")
     * @Apidoc\Param("strrand_ids", type="array", require=true, default="[1,2,3]", desc="所用字符")
     * @Apidoc\Param("strrand_len", type="int", require=true, default="12", desc="字符长度")
     * @Apidoc\Returned("len", type="int", desc="字符长度")
     * @Apidoc\Returned("ori", type="string", desc="原始字符")
     * @Apidoc\Returned("str", type="string", desc="生成字符")
     */
    public function strrand()
    {
        $param['strrand_ids'] = $this->param('ids/a', [1, 2, 3]);
        $param['strrand_len'] = $this->param('len/d', 12);

        validate(UtilsValidate::class)->scene('strrand')->check($param);

        $data = UtilsService::strrand($param['strrand_ids'], $param['strrand_len']);

        return success($data);
    }

    /**
     * @Apidoc\Title("字符串转换")
     * @Apidoc\Param("str", type="string", default="yylAdmin", desc="字符串")
     * @Apidoc\Returned("str", type="string", desc="字符串")
     * @Apidoc\Returned("len", type="int", desc="长度")
     * @Apidoc\Returned("lower", type="string", desc="小写")
     * @Apidoc\Returned("upper", type="string", desc="大写")
     * @Apidoc\Returned("rev", type="string", desc="翻转")
     * @Apidoc\Returned("md5", type="string", desc="MD5")
     */
    public function strtran()
    {
        $str = $this->param('str/s', '') ?: 'yylAdmin';

        $data = UtilsService::strtran($str);

        return success($data);
    }

    /**
     * @Apidoc\Title("时间戳转换")
     * @Apidoc\Param("type", type="string", default="timestamp", desc="转换类型，timestamp时间戳、datetime日期时间")
     * @Apidoc\Param("value", type="string", default="", desc="转换的值，时间戳、日期时间")
     * @Apidoc\Returned("type", type="string", desc="转换类型")
     * @Apidoc\Returned("value", type="string", desc="转换的值")
     * @Apidoc\Returned("timestamp", type="int", desc="时间戳")
     * @Apidoc\Returned("datetime", type="string", desc="日期时间")
     */
    public function timestamp()
    {
        $type  = $this->param('type', '') ?: 'timestamp';
        $value = $this->param('value', '') ?: time();

        $data = UtilsService::timestamp($type, $value);

        return success($data);
    }

    /**
     * @Apidoc\Title("字节转换")
     * @Apidoc\Param("type", type="string", default="B", desc="转换类型，b、B、KB、MB、GB、TB")
     * @Apidoc\Param("value", type="string", default="1024", desc="转换数值")
     * @Apidoc\Returned("b", type="int", desc="比特(b)")
     * @Apidoc\Returned("B", type="int", desc="字节(B)")
     * @Apidoc\Returned("KB", type="int", desc="千字节(KB)")
     * @Apidoc\Returned("MB", type="int", desc="兆字节(MB)")
     * @Apidoc\Returned("GB", type="int", desc="吉字节(GB)")
     * @Apidoc\Returned("TB", type="int", desc="太字节(TB)")
     */
    public function bytetran()
    {
        $type  = $this->param('type', '') ?: 'B';
        $value = $this->param('value', '') ?: 1024;

        $data = UtilsService::bytetran($type, $value);

        return success($data);
    }

    /**
     * @Apidoc\Title("IP信息")
     * @Apidoc\Param("ip", type="string", default="", desc="ip")
     * @Apidoc\Returned("ip", type="int", desc="ip")
     * @Apidoc\Returned("country", type="string", desc="国家")
     * @Apidoc\Returned("province", type="string", desc="省份")
     * @Apidoc\Returned("city", type="string", desc="城市")
     * @Apidoc\Returned("area", type="string", desc="区县")
     * @Apidoc\Returned("isp", type="string", desc="运营商")
     * @Apidoc\Returned("region", type="string", desc="地区（国省市区）")
     */
    public function ipinfo()
    {
        $ip = $this->param('ip/s', '') ?: $this->request->ip();

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
