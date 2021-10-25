<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口文档公共注释定义
namespace app\common\controller;

use hg\apidoc\annotation as Apidoc;

class ApidocDefinitions
{
    /**
     * 分页请求参数
     * @Apidoc\Param("page", type="int", default="1", desc="分页第几页")
     * @Apidoc\Param("limit", type="int", default="10", desc="分页每页数量")
     */
    public function pagingParam()
    {
    }

    /**
     * 分页返回参数
     * @Apidoc\Returned("count", type="int", default="0", desc="总数量")
     * @Apidoc\Returned("pages", type="int", default="0", desc="总页数")
     * @Apidoc\Returned("page", type="int", default="1", desc="分页第几页")
     * @Apidoc\Returned("limit", type="int", default="10", desc="分页每页数量")
     */
    public function pagingReturn()
    {
    }

    /**
     * 排序请求参数
     * @Apidoc\Param("sort_field", type="string", default=" ", desc="排序字段")
     * @Apidoc\Param("sort_value", type="string", default=" ", desc="排序类型：desc降序、asc升序")
     */
    public function sortParam()
    {
    }

    /**
     * 搜索请求参数
     * @Apidoc\Param("search_field", type="string", default=" ", desc="搜索字段")
     * @Apidoc\Param("search_value", type="string", default=" ", desc="搜索内容")
     */
    public function searchParam()
    {
    }

    /**
     * 日期请求参数
     * @Apidoc\Param("date_field", type="string", default=" ", desc="日期字段eg：create_time")
     * @Apidoc\Param("date_value", type="array", default=" ", desc="日期范围eg：['2020-02-02','2020-02-22']")
     */
    public function dateParam()
    {
    }

    /**
     * 验证码请求参数
     * @Apidoc\Param("captcha_id", type="string", default=" ", desc="验证码id")
     * @Apidoc\Param("captcha_code", type="string", default=" ", desc="验证码")
     */
    public function captchaParam()
    {
    }

    /**
     * 验证码返回参数
     * @Apidoc\Returned("captcha_switch", type="bool", default=" ", desc="验证码是否开启")
     * @Apidoc\Returned("captcha_id", type="string", default=" ", desc="验证码id")
     * @Apidoc\Returned("captcha_src", type="string", default=" ", desc="验证码图片")
     */
    public function captchaReturn()
    {
    }

    /**
     * 上传文件请求参数
     * @Apidoc\Param("file", type="file", require=true, default=" ", desc="图片、视频、文件")
     * @Apidoc\Param("type", type="string", require=false, default="image", desc="image、video、file")
     */
    public function fileParam()
    {
    }

    /**
     * 上传文件返回参数
     * @Apidoc\Returned("file_name", type="string", default=" ", desc="文件名称")
     * @Apidoc\Returned("file_path", type="string", default=" ", desc="文件路径")
     * @Apidoc\Returned("file_size", type="string", default=" ", desc="文件大小")
     * @Apidoc\Returned("file_url", type="string", default=" ", desc="文件链接")
     */
    public function fileReturn()
    {
    }
}
