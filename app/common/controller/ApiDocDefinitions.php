<?php
/*
 * @Description  : 接口文档公共注释定义
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-01
 * @LastEditTime : 2021-07-01
 */

namespace app\common\controller;

use hg\apidoc\annotation as Apidoc;

class ApiDocDefinitions
{
    /**
     * 请求头部：admin
     * @Apidoc\Header("AdminToken", type="string", require=true, default="", desc="admin_token")
     */
    public function headerAdmin()
    {
    }

    /**
     * 请求头部：index
     * @Apidoc\Header("MemberToken", type="string", require=true, default="", desc="member_token")
     */
    public function headerIndex()
    {
    }

    /**
     * 请求参数：分页
     * @Apidoc\Param("page", type="int",default="1", desc="分页第几页" )
     * @Apidoc\Param("limit", type="int",default="10", desc="分页每页数量" )
     * @Apidoc\Param("sort_field", type="string", default="", desc="排序字段" )
     * @Apidoc\Param("sort_type", type="string", default="", desc="排序类型：desc降序、asc升序" )
     */
    public function paramPaging()
    {
    }

    /**
     * 请求参数：日期
     * @Apidoc\Param("date_type", type="string", default="", desc="时间类型eg：create_time")
     * @Apidoc\Param("date_range", type="array", default="", desc="日期范围eg：[2020-02-02,2020-02-22]")
     */
    public function paramDate()
    {
    }

    /**
     * 请求参数：验证码
     * @Apidoc\Param("captcha_id", type="string", default="", desc="验证码id")
     * @Apidoc\Param("captcha_code", type="string", default="", desc="验证码")
     */
    public function paramCaptcha()
    {
    }

    /**
     * 返回参数：验证码
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *     @Apidoc\Returned("captcha_switch", type="bool", default="", desc="验证码是否开启"),
     *     @Apidoc\Returned("captcha_id", type="string", default="", desc="验证码id"),
     *     @Apidoc\Returned("captcha_src", type="string", default="", desc="验证码图片")
     * )
     */
    public function returnCaptcha()
    {
    }

    /**
     * 返回参数：分页
     * @Apidoc\Returned("count", type="int", desc="总数量")
     * @Apidoc\Returned("pages", type="int", desc="总页数")
     * @Apidoc\Returned("page", type="int", desc="第几页")
     * @Apidoc\Returned("limit", type="int", desc="每页数量")
     */
    public function returnPaging()
    {
    }

    /**
     * 返回参数：返回码、描述
     * @Apidoc\Returned("code", type="int", desc="返回码")
     * @Apidoc\Returned("msg", type="string", desc="返回描述")
     */
    public function returnCode()
    {
    }

    /**
     * 返回参数：返回数据
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function returnData()
    {
    }

    
    /**
     * 请求参数：上传文件
     * @Apidoc\Param("type", type="string", require=true, default="file", desc="image、video、file")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="图片、视频、文件")
     */
    public function paramFile()
    {
    }

    /**
     * 返回参数：上传文件
     * @Apidoc\Returned("data", type="object", desc="文件信息",
     *      @Apidoc\Returned("type", type="string", desc="类型"),
     *      @Apidoc\Returned("name", type="string", desc="名称"),
     *      @Apidoc\Returned("path", type="string", desc="路径"),
     *      @Apidoc\Returned("url", type="string", desc="链接"),
     *      @Apidoc\Returned("size", type="string", desc="大小"),
     * )
     */
    public function returnFile()
    {
    }
}
