<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 留言管理模型
namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class CommentModel extends Model
{
    protected $name = 'cms_comment';
    protected $pk = 'comment_id';

    /**
     * @Field("comment_id")
     */
    public function id()
    {
    }

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
     * @Field("comment")
     * @AddField("comment", type="array", require=true, default="", desc="留言列表")
     */
    public function comment()
    {
    }

    /**
     * @Field("comment_id")
     * @AddField("comment_id", type="int", require=false, default="", desc="留言ID")
     */
    public function search()
    {
    }
}
