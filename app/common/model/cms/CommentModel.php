<?php
/*
 * @Description  : 留言管理模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-13
 */

namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class CommentModel extends Model
{
    protected $name = 'cms_comment';
    protected $pk = 'comment_id';

    /**
     * @Field("comment_id,call,mobile,tel,title,remark,is_read,create_time,update_time,delete_time")
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
     * @Field("comment")
     * @AddField("comment", type="array", require=true, default="", desc="留言列表")
     */
    public function comment()
    {
    }

    /**
     * @Field("comment_id")
     */
    public function search()
    {
    }
}
