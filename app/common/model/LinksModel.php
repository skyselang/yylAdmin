<?php
/*
 * @Description  : 友链管理模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-01
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class LinksModel extends Model
{
    protected $name = 'links';
    protected $pk = 'links_id';

    /**
     * @Field("links_id,name,url,sort,is_top,is_hot,is_rec,is_hide,create_time,update_time")
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
     * @Field("name,url,sort")
     */
    public function add()
    {
    }

    /**
     * @Field("links_id,name,url,sort")
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
     * @Field("links_id")
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
     * @Field("links_id,name")
     * @AddField("links_id", type="int", require=false, default="", desc="id")
     * @AddField("name", type="string", require=false, default="", desc="名称")
     */
    public function search()
    {
    }

    /**
     * @Field("imgs")
     * @AddField("imgs", type="array", require=false, default="[]", desc="图片",
     *      @Param("name", type="string", require=true, default="", desc="名称"),
     *      @Param("path", type="string", require=true, default="", desc="路径"),
     *      @Param("url", type="string", require=true, default="", desc="链接"),
     *      @Param("size", type="string", require=true, default="", desc="大小")
     * )
     */
    public function imgs()
    {
    }
}
