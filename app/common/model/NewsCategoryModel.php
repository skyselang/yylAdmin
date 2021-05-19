<?php
/*
 * @Description  : 新闻分类模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-19
 * @LastEditTime : 2021-05-19
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;

class NewsCategoryModel extends Model
{
    protected $name = 'news_category';

    /**
     * @Field("news_category_id")
     */
    public function id()
    {
    }
    
    /**
     * @Field("news_category_id,category_name,category_sort,is_hide,create_time,update_time")
     */
    public function list()
    {
    }

    /**
     * 
     */
    public function info()
    {
    }

    /**
     * @Field("category_name,category_sort")
     */
    public function add()
    {
    }

    /**
     * @Field("news_category_id,category_name,category_sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("news_category_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("news_category_id,is_hide")
     */
    public function ishide()
    {
    }
}
