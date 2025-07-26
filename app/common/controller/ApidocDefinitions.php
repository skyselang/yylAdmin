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
     * @Apidoc\Query("page", type="int", default="1", desc="lang(分页第几页)")
     * @Apidoc\Query("limit", type="int", default="10", desc="lang(分页每页数量)")
     */
    public function pagingQuery() {}

    /**
     * 分页返回参数
     * @Apidoc\Returned("count", type="int", default="0", desc="lang(总数量)")
     * @Apidoc\Returned("pages", type="int", default="0", desc="lang(总页数)")
     * @Apidoc\Returned("page", type="int", default="1", desc="lang(分页第几页)")
     * @Apidoc\Returned("limit", type="int", default="10", desc="lang(分页每页数量)")
     */
    public function pagingReturn() {}

    /**
     * 字段查询方式返回参数
     * @Apidoc\Returned("exps", type="array", desc="lang(查询方式)", children={
     *   @Apidoc\Returned("exp", type="string", default="", desc="lang(查询方式，eg：=)"),
     *   @Apidoc\Returned("name", type="string", default="", desc="lang(查询名称，eg：等于)"),
     * })
     */
    public function expsReturn() {}

    /**
     * 排序请求参数query
     * @Apidoc\Query("sort_field", type="string", default="", desc="lang(排序字段，eg：sort)")
     * @Apidoc\Query("sort_value", type="string", default="", desc="lang(排序类型：desc降序、asc升序)")
     */
    public function sortQuery() {}

    /**
     * 排序请求参数param
     * @Apidoc\Param("sort_field", type="string", default="", desc="lang(排序字段，eg：sort)")
     * @Apidoc\Param("sort_value", type="string", default="", desc="lang(排序类型：desc降序、asc升序)")
     */
    public function sortParam() {}

    /**
     * 字段查询请求参数query
     * @Apidoc\Query("search", type="array", default="[]", desc="lang(查询参数)", children={
     *   @Apidoc\Query("field", type="string", default="", desc="lang(查询字段，eg：name)"),
     *   @Apidoc\Query("exp", type="string", default="", desc="lang(查询方式，eg：=)"),
     *   @Apidoc\Query("value", type="string", default="", desc="lang(查询内容，eg：张三)"),
     * })
     */
    public function searchQuery() {}

    /**
     * 字段查询请求参数param
     * @Apidoc\Param("search", type="array", default="[]", desc="lang(查询参数)", children={
     *   @Apidoc\Param("field", type="string", default="", desc="lang(查询字段，eg：name)"),
     *   @Apidoc\Param("exp", type="string", default="", desc="lang(查询方式，eg：=)"),
     *   @Apidoc\Param("value", type="string", default="", desc="lang(查询内容，eg：张三)"),
     * })
     */
    public function searchParam() {}

    /**
     * 验证码请求参数
     * @Apidoc\Param("captcha_id", type="string", default="", desc="lang(字符，验证码id)")
     * @Apidoc\Param("captcha_code", type="string", default="", desc="lang(字符，验证码内容)")
     * @Apidoc\Param("ajcaptcha", type="object", default="", desc="lang(行为，验证码内容)", children={
     *   @Apidoc\Param("captchaVerification", type="string", default="", desc="lang(验证码内容)")
     * })
     */
    public function captchaParam() {}

    /**
     * 验证码返回参数
     * @Apidoc\Returned("captcha_switch", type="bool", default="", desc="lang(验证码是否开启)")
     * @Apidoc\Returned("captcha_id", type="string", default="", desc="lang(字符，验证码id)")
     * @Apidoc\Returned("captcha_img", type="string", default="", desc="lang(字符，验证码图片base64)")
     * @Apidoc\Returned("error", type="bool", default="", desc="lang(行为，)")
     * @Apidoc\Returned("repCode", type="int", default="", desc="lang(行为，)")
     * @Apidoc\Returned("repData", type="object", default="", desc="lang(行为，)", children={
     *   @Apidoc\Returned("jigsawImageBase64", type="string", default="", desc="lang(滑块图base64)"),
     *   @Apidoc\Returned("originalImageBase64", type="string", default="", desc="lang(底图base64)"),
     *   @Apidoc\Returned("secretKey", type="string", default="", desc="lang(secretKey)"),
     *   @Apidoc\Returned("token", type="string", default="", desc="lang(一次校验唯一编号)")
     * })
     * @Apidoc\Returned("repMsg", type="string", default="", desc="lang(行为，)")
     * @Apidoc\Returned("success", type="bool", default="", desc="lang(行为，)")
     */
    public function captchaReturn() {}

    /**
     * 上传文件列表请求参数
     * @Apidoc\Param("file_id", type="int", require=true, default="", desc="lang(文件ID)", mock="@natural(1,50)")
     */
    public function filesParam() {}

    /**
     * 上传文件请求参数
     * @Apidoc\Param("file", type="file", require=true, default="", desc="lang(文件)")
     */
    public function fileParam() {}

    /**
     * 上传文件返回参数
     * @Apidoc\Returned("file_id", type="int", default="", desc="lang(文件ID)")
     * @Apidoc\Returned("file_name", type="string", default="", desc="lang(文件名称)")
     * @Apidoc\Returned("file_path", type="string", default="", desc="lang(文件路径)")
     * @Apidoc\Returned("file_size", type="string", default="", desc="lang(文件大小)")
     * @Apidoc\Returned("file_url", type="string", default="", desc="lang(文件链接)")
     */
    public function fileReturn() {}

    /**
     * ids请求参数
     * @Apidoc\Param("ids", type="array", require=true, default="", desc="lang(id数组，eg：[1,2,3])")
     */
    public function idsParam() {}

    /**
     * images请求参数
     * @Apidoc\Param("images", type="array", require=false, default="[]", desc="lang(图片列表)", children={
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="lang(文件ID)", mock="@natural(1,50)"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="lang(图片名称)"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="lang(图片大小)"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="lang(图片路径)"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="lang(图片链接)"),
     * })
     */
    public function imagesParam() {}

    /**
     * images返回参数
     * @Apidoc\Returned("images", type="array", require=false, default="[]", desc="lang(图片列表)", children={
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="lang(文件ID)"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="lang(图片名称)"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="lang(图片大小)"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="lang(图片路径)"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="lang(图片链接)"),
     * })
     */
    public function imagesReturn() {}

    /**
     * videos请求参数
     * @Apidoc\Param("videos", type="array", require=false, default="[]", desc="lang(视频列表)", children={
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="lang(文件ID)", mock="@natural(1,50)"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="lang(视频名称)"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="lang(视频大小)"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="lang(视频路径)"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="lang(视频链接)"),
     * })
     */
    public function videosParam() {}

    /**
     * videos返回参数
     * @Apidoc\Returned("videos", type="array", require=false, default="[]", desc="lang(视频列表)", children={
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="lang(文件ID)"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="lang(视频名称)"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="lang(视频大小)"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="lang(视频路径)"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="lang(视频链接)"),
     * })
     */
    public function videosReturn() {}

    /**
     * audios请求参数
     * @Apidoc\Param("audios", type="array", require=false, default="[]", desc="lang(音频列表)", children={
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="lang(文件ID)", mock="@natural(1,50)"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="lang(音频名称)"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="lang(音频大小)"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="lang(音频路径)"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="lang(音频链接)"),
     * })
     */
    public function audiosParam() {}

    /**
     * audios返回参数
     * @Apidoc\Returned("audios", type="array", require=false, default="[]", desc="lang(音频列表)", children={
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="lang(文件ID)"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="lang(音频名称)"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="lang(音频大小)"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="lang(音频路径)"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="lang(音频链接)"),
     * })
     */
    public function audiosReturn() {}

    /**
     * words请求参数
     * @Apidoc\Param("words", type="array", require=false, default="[]", desc="lang(文档列表)", children={
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="lang(文件ID)", mock="@natural(1,50)"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="lang(文档名称)"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="lang(文档大小)"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="lang(文档路径)"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="lang(文档链接)"),
     * })
     */
    public function wordsParam() {}

    /**
     * words返回参数
     * @Apidoc\Returned("words", type="array", require=false, default="[]", desc="lang(文档列表)", children={
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="lang(文件ID)"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="lang(文档名称)"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="lang(文档大小)"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="lang(文档路径)"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="lang(文档链接)"),
     * })
     */
    public function wordsReturn() {}

    /**
     * others请求参数
     * @Apidoc\Param("others", type="array", require=false, default="[]", desc="lang(附件列表)", children={
     *   @Apidoc\Param("file_id", type="int", require=true, default="", desc="lang(文件ID)", mock="@natural(1,50)"),
     *   @Apidoc\Param("file_name", type="string", require=false, default="", desc="lang(附件名称)"),
     *   @Apidoc\Param("file_size", type="string", require=false, default="", desc="lang(附件大小)"),
     *   @Apidoc\Param("file_path", type="string", require=false, default="", desc="lang(附件路径)"),
     *   @Apidoc\Param("file_url", type="string", require=false, default="", desc="lang(附件链接)"),
     * })
     */
    public function othersParam() {}

    /**
     * others返回参数
     * @Apidoc\Returned("others", type="array", require=false, default="[]", desc="lang(附件列表)", children={
     *   @Apidoc\Returned("file_id", type="int", require=true, default="", desc="lang(文件ID)"),
     *   @Apidoc\Returned("file_name", type="string", require=true, default="", desc="lang(附件名称)"),
     *   @Apidoc\Returned("file_size", type="string", require=true, default="", desc="lang(附件大小)"),
     *   @Apidoc\Returned("file_path", type="string", require=true, default="", desc="lang(附件路径)"),
     *   @Apidoc\Returned("file_url", type="string", require=true, default="", desc="lang(附件链接)"),
     * })
     */
    public function othersReturn() {}

    /**
     * 是否禁用请求参数
     * @Apidoc\Param("ids", type="array", require=true, default="", desc="lang(id数组，eg：[1,2,3])")
     * @Apidoc\Param("is_disable", type="int", default="0", desc="lang(是否禁用，1是0否)")
     */
    public function disableParam() {}

    /**
     * 批量修改请求参数
     * @Apidoc\Param("ids", type="array", require=true, default="", desc="lang(id数组，eg：[1,2,3])")
     * @Apidoc\Param("field", type="string", require=true, desc="lang(字段名，eg：sort)")
     * @Apidoc\Param("value", type="any", require=true, desc="lang(字段值，eg：250)")
     */
    public function updateParam() {}

    /**
     * 导出参数
     * @Apidoc\Query("file_path", type="string", desc="lang(文件路径)")
     * @Apidoc\Query("file_name", type="string", desc="lang(文件名称)")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param("export_remark", type="string", desc="lang(导出备注)")
     * @Apidoc\Param("ids", type="array", desc="lang(id数组，eg：[1,2,3])")
     */
    public function exportParam() {}

    /**
     * 导入参数
     * @Apidoc\Query("file_path", type="string", desc="lang(文件路径)")
     * @Apidoc\Query("file_name", type="string", desc="lang(文件名称)")
     * @Apidoc\Param("import_file", type="file", require=true, desc="lang(导入文件)")
     * @Apidoc\Param("is_update", type="int", default="0", desc="lang(是否更新)")
     * @Apidoc\Param("import_remark", type="string", default="", desc="lang(导入备注)")
     * @Apidoc\Returned("import_num", type="int", desc="lang(导入数量)")
     * @Apidoc\Returned("success_num", type="int", desc="lang(成功数量)")
     * @Apidoc\Returned("fail_num", type="int", desc="lang(失败数量)")
     * @Apidoc\Returned("header", type="array", desc="lang(表头列表)")
     * @Apidoc\Returned("success", type="array", desc="lang(成功列表)")
     * @Apidoc\Returned("fail", type="array", desc="lang(失败列表)")
     */
    public function importParam() {}
}
