<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容管理模型
namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class ContentModel extends Model
{
    // 表名
    protected $name = 'cms_content';
    // 表主键
    protected $pk = 'content_id';

    /**
     * @Apidoc\Field("content_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("content_id,name,category_id,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time,delete_time")
     * @Apidoc\AddField("img_url", type="string", require=true, default=" ", desc="图片链接")
     */
    public function listReturn()
    {
    }

    /**
     * 
     */
    public function infoReturn()
    {
    }

    /**
     * @Apidoc\Field("category_id,name,title,keywords,description,content,url,sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("content_id,category_id,name,title,keywords,description,content,url,sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("name")
     */
    public function name()
    {
    }

    /**
     * @Apidoc\Field("category_id")
     */
    public function category_id()
    {
    }

    /**
     * @Apidoc\Field("is_top")
     */
    public function is_top()
    {
    }

    /**
     * @Apidoc\Field("is_hot")
     */
    public function is_hot()
    {
    }

    /**
     * @Apidoc\Field("is_rec")
     */
    public function is_rec()
    {
    }

    /**
     * @Apidoc\Field("is_hide")
     */
    public function is_hide()
    {
    }

    /**
     * @Apidoc\Field("imgs")
     * @Apidoc\AddField("imgs", type="array", require=false, default="[]", desc="图片",
     *    @Apidoc\Param("file_name", type="string", require=true, default=" ", desc="图片名称"),
     *    @Apidoc\Param("file_size", type="string", require=true, default=" ", desc="图片大小"),
     *    @Apidoc\Param("file_path", type="string", require=true, default=" ", desc="图片路径"),
     *    @Apidoc\Param("file_url", type="string", require=true, default=" ", desc="图片链接"),
     * )
     */
    public function imgs()
    {
    }

    /**
     * @Apidoc\Field("files")
     * @Apidoc\AddField("files", type="array", require=false, default="[]", desc="附件",
     *    @Apidoc\Param("file_name", type="string", require=true, default=" ", desc="附件名称"),
     *    @Apidoc\Param("file_size", type="string", require=true, default=" ", desc="附件大小"),
     *    @Apidoc\Param("file_path", type="string", require=true, default=" ", desc="附件路径"),
     *    @Apidoc\Param("file_url", type="string", require=true, default=" ", desc="附件链接"),
     * )
     */
    public function files()
    {
    }

    /**
     * @Apidoc\Field("videos")
     * @Apidoc\AddField("videos", type="array", require=false, default="[]", desc="视频",
     *    @Apidoc\Param("file_name", type="string", require=true, default=" ", desc="视频名称"),
     *    @Apidoc\Param("file_size", type="string", require=true, default=" ", desc="视频大小"),
     *    @Apidoc\Param("file_path", type="string", require=true, default=" ", desc="视频路径"),
     *    @Apidoc\Param("file_url", type="string", require=true, default=" ", desc="视频链接"),
     * )
     */
    public function videos()
    {
    }

    /**
     * @Apidoc\Field("content")
     * @Apidoc\AddField("content", type="array", require=true, default=" ", desc="内容列表")
     */
    public function content()
    {
    }
}
