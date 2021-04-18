<?php
/*
 * @Description  : 新闻模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-09
 * @LastEditTime : 2021-04-09
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;

class NewsModel extends Model
{
    protected $name = 'news';

    /**
     * @Field("news_id")
     */
    public function id()
    {
    }
    
    /**
     * @Field("news_id,img,title,time,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time")
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
     * @Field("img,title,intro,author,time,source,source_url,content,sort")
     */
    public function add()
    {
    }

    /**
     * @Field("news_id,img,title,intro,author,time,source,source_url,content,sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("news_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("news_id,is_top")
     */
    public function istop()
    {
    }

    /**
     * @Field("news_id,is_hot")
     */
    public function ishot()
    {
    }

    /**
     * @Field("news_id,is_rec")
     */
    public function isrec()
    {
    }

    /**
     * @Field("news_id,is_hide")
     */
    public function ishide()
    {
    }
}
