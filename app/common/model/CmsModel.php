<?php
/*
 * @Description  : 内容管理模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-12
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class CmsModel extends Model
{
    protected $name = 'cms';
    protected $pk = 'cms_id';

    /**
     * @Field("cms_id,name,category_id,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time")
     * @AddField("img_url", type="string", require=true, default="", desc="图片链接")
     */
    public function list()
    {
    }

    /**
     * @WithoutField("is_delete,delete_time")
     */
    public function info()
    {
    }

    /**
     * @Field("category_id,name,title,keywords,description,content,url,sort")
     */
    public function add()
    {
    }

    /**
     * @Field("cms_id,category_id,name,title,keywords,description,content,url,sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("is_top")
     */
    public function istop()
    {
    }

    /**
     * @Field("is_hot")
     */
    public function ishot()
    {
    }

    /**
     * @Field("is_rec")
     */
    public function isrec()
    {
    }

    /**
     * @Field("is_hide")
     */
    public function ishide()
    {
    }

    /**
     * @Field("cms")
     * @AddField("cms", type="array", require=true, default="", desc="内容列表")
     */
    public function cms()
    {
    }

    /**
     * @Field("cms_id")
     */
    public function id()
    {
    }

    /**
     * @Field("name")
     */
    public function name()
    {
    }

    /**
     * @Field("imgs")
     * @AddField("imgs", type="array", require=false, default="[]", desc="图片",
     *      @Param("name", type="string", require=true, default="", desc="名称"),
     *      @Param("path", type="string", require=true, default="", desc="路径"),
     *      @Param("url", type="string", require=true, default="", desc="链接"),
     *      @Param("size", type="string", require=true, default="", desc="大小"),
     * )
     */
    public function imgs()
    {
    }

    /**
     * @Field("files")
     * @AddField("files", type="array", require=false, default="[]", desc="附件",
     *      @Param("name", type="string", require=true, default="", desc="名称"),
     *      @Param("path", type="string", require=true, default="", desc="路径"),
     *      @Param("url", type="string", require=true, default="", desc="链接"),
     *      @Param("size", type="string", require=true, default="", desc="大小"),
     * )
     */
    public function files()
    {
    }

    /**
     * @Field("videos")
     * @AddField("videos", type="array", require=false, default="[]", desc="视频",
     *      @Param("name", type="string", require=true, default="", desc="名称"),
     *      @Param("path", type="string", require=true, default="", desc="路径"),
     *      @Param("url", type="string", require=true, default="", desc="链接"),
     *      @Param("size", type="string", require=true, default="", desc="大小"),
     * )
     */
    public function videos()
    {
    }

    /**
     * @Field("cms_id,name,category_id")
     * @AddField("cms_id", type="int", require=false, default="", desc="id")
     * @AddField("name", type="string", require=false, default="", desc="名称")
     * @AddField("category_id", type="int", require=false, default="", desc="分类id")
     */
    public function search()
    {
    }

    /**
     * @Field("name,category_id")
     * @AddField("name", type="string", require=false, default="", desc="名称")
     * @AddField("category_id", type="int", require=false, default="", desc="分类id")
     */
    public function indexList()
    {
    }

    /**
     * @Field("cms_id,category_id")
     */
    public function indexInfo()
    {
    }
}
