<?php
/*
 * @Description  : 视频分类模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-19
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;

class VideoCategoryModel extends Model
{
    protected $name = 'video_category';
    protected $pk = 'video_category_id';

    /**
     * @Field("video_category_id,video_category_pid,category_name,keywords,description,img,sort,is_hide,create_time,update_time")
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
     * @Field("video_category_pid,category_name,title,keywords,description,img,sort")
     */
    public function add()
    {
    }

    /**
     * @Field("video_category_id,video_category_pid,category_name,title,keywords,description,img,sort")
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
     * @Field("video_category_id")
     */
    public function id()
    {
    }

    /**
     * @Field("video_category_pid")
     */
    public function pid()
    {
    }

    /**
     * @Field("category_name")
     */
    public function category_name()
    {
    }

    /**
     * @Field("video_category_id,video_category_pid,category_name,sort")
     */
    public function category()
    {
    }
}
