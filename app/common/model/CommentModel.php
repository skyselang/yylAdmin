<?php
/*
 * @Description  : 留言管理模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class CommentModel extends Model
{
    protected $name = 'comment';
    protected $pk = 'comment_id';

    /**
     * @Field("comment_id,call,mobile,tel,title,remark,is_read,create_time,update_time")
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
     * @Field("call,mobile,tel,email,qq,wechat,title,content")
     */
    public function add()
    {
    }

    /**
     * @Field("comment_id,remark")
     */
    public function edit()
    {
    }

    /**
     * @Field("comment_id")
     */
    public function id()
    {
    }

    /**
     * @Field("comment_id")
     */
    public function search()
    {
    }
}
