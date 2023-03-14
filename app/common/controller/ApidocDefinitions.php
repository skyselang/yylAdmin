<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\controller;

use hg\apidoc\annotation as Apidoc;

/**
 * Apidoc 公共注释定义
 */
class ApidocDefinitions
{
    /**
     * 分页请求参数
     * @Apidoc\Query("page", type="int", default="1", desc="分页第几页")
     * @Apidoc\Query("limit", type="int", default="10", desc="分页每页数量")
     */
    public function pagingQuery()
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
     * @Apidoc\Query("sort_field", type="string", default="", desc="排序字段，eg：sort")
     * @Apidoc\Query("sort_value", type="string", default="", desc="排序类型：desc降序、asc升序")
     */
    public function sortQuery()
    {
    }

    /**
     * 字段查询方式返回参数
     * @Apidoc\Returned("exps", type="array", desc="查询方式",
     *   @Apidoc\Returned("exp", type="string", default="", desc="查询方式，eg：="),
     *   @Apidoc\Returned("name", type="string", default="", desc="查询名称，eg：等于"),
     * )
     */
    public function expsReturn()
    {
    }

    /**
     * 字段查询请求参数
     * @Apidoc\Query("search_field", type="string", default="", desc="查询字段，eg：name")
     * @Apidoc\Query("search_exp", type="string", default="", desc="查询方式，eg：=")
     * @Apidoc\Query("search_value", type="string", default="", desc="查询内容，eg：张三")
     */
    public function searchQuery()
    {
    }

    /**
     * 日期查询请求参数
     * @Apidoc\Query("date_field", type="string", default="", desc="日期字段，eg：create_time")
     * @Apidoc\Query("date_value", type="array", default="", desc="日期范围，eg：['2020-02-02','2020-02-22']")
     */
    public function dateQuery()
    {
    }

    /**
     * 验证码请求参数
     * @Apidoc\Param("captcha_id", type="string", default="", desc="字符，验证码id")
     * @Apidoc\Param("captcha_code", type="string", default="", desc="字符，验证码内容")
     * @Apidoc\Param("ajcaptcha", type="object", default="", desc="行为，验证码内容", 
     *   @Apidoc\Param("captchaVerification", type="string", default="", desc="验证码内容")
     * )
     */
    public function captchaParam()
    {
    }

    /**
     * 验证码返回参数
     * @Apidoc\Returned("captcha_switch", type="bool", default="", desc="验证码是否开启")
     * @Apidoc\Returned("captcha_id", type="string", default="", desc="字符，验证码id")
     * @Apidoc\Returned("captcha_src", type="string", default="", desc="字符，验证码图片")
     * @Apidoc\Returned("error", type="bool", default="", desc="行为，")
     * @Apidoc\Returned("repCode", type="int", default="", desc="行为，")
     * @Apidoc\Returned("repData", type="object", default="", desc="行为，",
     *   @Apidoc\Returned("jigsawImageBase64", type="string", default="", desc="滑块图base64"),
     *   @Apidoc\Returned("originalImageBase64", type="string", default="", desc="底图base64"),
     *   @Apidoc\Returned("secretKey", type="string", default="", desc="secretKey"),
     *   @Apidoc\Returned("token", type="string", default="", desc="一次校验唯一标识")
     * )
     * @Apidoc\Returned("repMsg", type="string", default="", desc="行为，")
     * @Apidoc\Returned("success", type="bool", default="", desc="行为，")
     */
    public function captchaReturn()
    {
    }

    /**
     * 上传文件请求参数
     * @Apidoc\Param("file", type="file", require=true, default="", desc="文件")
     */
    public function fileParam()
    {
    }

    /**
     * 上传文件返回参数
     * @Apidoc\Returned("file_id", type="int", default="", desc="文件ID")
     * @Apidoc\Returned("file_name", type="string", default="", desc="文件名称")
     * @Apidoc\Returned("file_path", type="string", default="", desc="文件路径")
     * @Apidoc\Returned("file_size", type="string", default="", desc="文件大小")
     * @Apidoc\Returned("file_url", type="string", default="", desc="文件链接")
     */
    public function fileReturn()
    {
    }

    /**
     * ids请求参数
     * @Apidoc\Param("ids", type="array", require=true, default="", desc="id数组，eg：[1,2,3]")
     */
    public function idsParam()
    {
    }

    /**
     * images请求参数
     * @Apidoc\Param("images", type="array", require=false, default="[]", desc="图片",
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="图片名称"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="图片大小"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="图片路径"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="图片链接")
     * )
     */
    public function imagesParam()
    {
    }

    /**
     * images返回参数
     * @Apidoc\Returned("images", type="array", require=false, default="[]", desc="图片",
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="图片名称"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="图片大小"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="图片路径"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="图片链接")
     * )
     */
    public function imagesReturn()
    {
    }

    /**
     * videos请求参数
     * @Apidoc\Param("videos", type="array", require=false, default="[]", desc="视频",
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="视频名称"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="视频大小"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="视频路径"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="视频链接"),
     * )
     */
    public function videosParam()
    {
    }

    /**
     * videos返回参数
     * @Apidoc\Returned("videos", type="array", require=false, default="[]", desc="视频",
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="视频名称"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="视频大小"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="视频路径"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="视频链接"),
     * )
     */
    public function videosReturn()
    {
    }

    /**
     * audios请求参数
     * @Apidoc\Param("audios", type="array", require=false, default="[]", desc="音频",
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="音频名称"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="音频大小"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="音频路径"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="音频链接"),
     * )
     */
    public function audiosParam()
    {
    }

    /**
     * audios返回参数
     * @Apidoc\Returned("audios", type="array", require=false, default="[]", desc="音频",
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="音频名称"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="音频大小"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="音频路径"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="音频链接"),
     * )
     */
    public function audiosReturn()
    {
    }

    /**
     * words请求参数
     * @Apidoc\Param("words", type="array", require=false, default="[]", desc="文档",
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="文档名称"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="文档大小"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="文档路径"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="文档链接"),
     * )
     */
    public function wordsParam()
    {
    }

    /**
     * words返回参数
     * @Apidoc\Returned("words", type="array", require=false, default="[]", desc="文档",
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="文档名称"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="文档大小"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="文档路径"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="文档链接"),
     * )
     */
    public function wordsReturn()
    {
    }

    /**
     * others请求参数
     * @Apidoc\Param("others", type="array", require=false, default="[]", desc="附件",
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="附件名称"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="附件大小"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="附件路径"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="附件链接"),
     * )
     */
    public function othersParam()
    {
    }

    /**
     * others返回参数
     * @Apidoc\Returned("others", type="array", require=false, default="[]", desc="附件",
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="附件名称"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="附件大小"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="附件路径"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="附件链接"),
     * )
     */
    public function othersReturn()
    {
    }

    /**
     * 自定义设置请求参数
     * @Apidoc\Param("diy_config", type="array", default="", desc="自定义设置",
     *   @Apidoc\Param("config_key", type="string", require=true, default="", desc="键名"),
     *   @Apidoc\Param("config_val", type="string", require=false, default="", desc="键值"),
     *   @Apidoc\Param("config_desc", type="string", require=false, default="", desc="说明")
     * )
     */
    public function diyConParam()
    {
    }

    /**
     * 自定义设置返回参数
     * @Apidoc\Returned("diy_config", type="array", default="", desc="自定义设置",
     *   @Apidoc\Returned("config_key", type="string", require=true, default="", desc="键名"),
     *   @Apidoc\Returned("config_val", type="string", require=false, default="", desc="键值"),
     *   @Apidoc\Returned("config_desc", type="string", require=false, default="", desc="说明")
     * )
     */
    public function diyConReturn()
    {
    }

    /**
     * 自定义设置对象返回参数
     * @Apidoc\Returned("diy_con_obj", type="object", desc="自定义设置对象")
     */
    public function diyConObjReturn()
    {
    }
}
