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
use hg\apidoc\annotation as Apidoc;

class CategoryModel extends Model
{
    // 表名
    protected $name = 'cms_category';
    // 表主键
    protected $pk = 'category_id';

    /**
     * @Apidoc\Field("category_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("category_id,category_pid,category_name,sort,is_hide,create_time,update_time")
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
     * @Apidoc\Field("category_id,category_pid,category_name,title,keywords,description,sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("category_id,category_pid,category_name,title,keywords,description,sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("category_pid")
     */
    public function category_pid()
    {
    }

    /**
     * @Apidoc\Field("category_name")
     */
    public function category_name()
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
     *    @Apidoc\Param("file_url", type="string", require=true, default=" ", desc="图片链接")
     * )
     */
    public function imgs()
    {
    }

    /**
     * @Apidoc\Field("category")
     * @Apidoc\AddField("category", type="array", require=true, default=" ", desc="内容分类列表")
     */
    public function category()
    {
    }
}
