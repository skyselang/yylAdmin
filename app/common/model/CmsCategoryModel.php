<?php
/*
 * @Description  : 内容分类模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-09
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class CmsCategoryModel extends Model
{
    protected $name = 'cms_category';
    protected $pk = 'category_id';

    /**
     * @Field("category_id,category_pid,category_name,sort,is_hide,create_time,update_time")
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
     * @Field("category_id,category_pid,category_name,title,keywords,description,sort")
     */
    public function add()
    {
    }

    /**
     * @Field("category_id,category_pid,category_name,title,keywords,description,sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("is_hide")
     */
    public function ishide()
    {
    }

    /**
     * @Field("category_id")
     */
    public function id()
    {
    }

    /**
     * @Field("category_pid")
     */
    public function pid()
    {
    }

    /**
     * @Field("category_name")
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
     *      @Param("size", type="string", require=true, default="", desc="大小")
     * )
     */
    public function imgs()
    {
    }

    /**
     * @Field("category")
     * @AddField("category_id", type="array", require=true, default="", desc="内容分类列表")
     */
    public function category()
    {
    }
}
