<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 内容管理模型
 */
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
     * 列表
     * @Apidoc\Field("content_id,name,img_id,category_id,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time,delete_time")
     * @Apidoc\AddField("img_url", type="string", default="", desc="封面图片链接")
     */
    public function listReturn()
    {
    }

    /**
     * 信息
     */
    public function infoReturn()
    {
    }

    /**
     * 添加
     * @Apidoc\Field("category_id,name,img_id,title,keywords,description,author,url,sort,content")
     */
    public function addParam()
    {
    }

    /**
     * 修改
     * @Apidoc\Field("content_id,category_id,name,img_id,title,keywords,description,author,url,sort,content")
     */
    public function editParam()
    {
    }

    /**
     * 名称
     * @Apidoc\Field("name")
     */
    public function name()
    {
    }

    /**
     * 分类id
     * @Apidoc\Field("category_id")
     */
    public function category_id()
    {
    }

    /**
     * 是否置顶
     * @Apidoc\Field("is_top")
     */
    public function is_top()
    {
    }

    /**
     * 是否热门
     * @Apidoc\Field("is_hot")
     */
    public function is_hot()
    {
    }

    /**
     * 是否推荐
     * @Apidoc\Field("is_rec")
     */
    public function is_rec()
    {
    }

    /**
     * 是否隐藏
     * @Apidoc\Field("is_hide")
     */
    public function is_hide()
    {
    }
}
