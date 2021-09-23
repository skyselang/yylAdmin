<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容分类模型
namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class CategoryModel extends Model
{
    protected $name = 'cms_category';
    protected $pk = 'category_id';

    /**
     * @Field("category_id")
     */
    public function id()
    {
    }

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
     *      @Param("file_name", type="string", require=true, default="", desc="图片名称"),
     *      @Param("file_path", type="string", require=true, default="", desc="图片路径"),
     *      @Param("file_url", type="string", require=true, default="", desc="图片链接"),
     *      @Param("file_size", type="string", require=true, default="", desc="图片大小")
     * )
     */
    public function imgs()
    {
    }

    /**
     * @Field("category")
     * @AddField("category", type="array", require=true, default="", desc="内容分类列表")
     */
    public function category()
    {
    }
}
