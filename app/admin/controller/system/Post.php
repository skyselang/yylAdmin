<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\validate\system\PostValidate;
use app\common\service\system\PostService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("职位管理")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("400")
 */
class Post extends BaseController
{
    /**
     * @Apidoc\Title("职位列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\system\PostModel", type="tree", desc="职位树形", field="post_id,post_pid,post_name,post_abbr,post_desc,sort,is_disable,create_time,update_time")
     * @Apidoc\Returned("tree", ref="app\common\model\system\PostModel", type="tree", desc="职位树形", field="post_id,post_pid,post_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data['list']  = PostService::list('tree', $where);
        $data['tree']  = PostService::list('tree', [where_delete()], [], 'post_id,post_pid,post_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        if (count($where) > 1) {
            $list = tree_to_list($data['list']);
            $all  = tree_to_list($data['tree']);
            $pk   = 'post_id';
            $pid  = 'post_pid';
            $ids  = [];
            foreach ($list as $val) {
                $pids = children_parent_ids($all, $val[$pk], $pk, $pid);
                $cids = parent_children_ids($all, $val[$pk], $pk, $pid);
                $ids  = array_merge($ids, $pids, $cids);
            }
            $data['list'] = PostService::list('tree', [[$pk, 'in', $ids], where_delete()]);
        }

        $post = PostService::list('list', $where, [], 'post_id');
        $data['count'] = count($post);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位信息")
     * @Apidoc\Query(ref="app\common\model\system\PostModel", field="post_id")
     * @Apidoc\Returned(ref="app\common\model\system\PostModel")
     */
    public function info()
    {
        $param = $this->params(['post_id/d' => '']);

        validate(PostValidate::class)->scene('info')->check($param);

        $data = PostService::info($param['post_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\PostModel", field="post_pid,post_name,post_abbr,post_desc,sort")
     */
    public function add()
    {
        $param = $this->params(PostService::$edit_field);

        validate(PostValidate::class)->scene('add')->check($param);

        $data = PostService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\PostModel", field="post_id,post_pid,post_name,post_abbr,post_desc,sort")
     */
    public function edit()
    {
        $param = $this->params(PostService::$edit_field);

        validate(PostValidate::class)->scene('edit')->check($param);

        $data = PostService::edit($param['post_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(PostValidate::class)->scene('dele')->check($param);

        $data = PostService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\PostModel", field="post_pid")
     */
    public function editpid()
    {
        $param = $this->params(['ids/a' => [], 'post_pid/d' => 0]);

        validate(PostValidate::class)->scene('editpid')->check($param);

        $data = PostService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\PostModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(PostValidate::class)->scene('disable')->check($param);

        $data = PostService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("职位用户")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\system\PostModel", field="post_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="用户列表", children={
     *   @Apidoc\Returned(ref="app\common\model\system\UserModel", field="user_id,nickname,username,sort,is_super,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\system\UserModel\getAvatarUrlAttr", field="avatar_url"),
     *   @Apidoc\Returned(ref="app\common\model\system\UserModel\getDeptNamesAttr", field="dept_names"),
     *   @Apidoc\Returned(ref="app\common\model\system\UserModel\getPostNamesAttr", field="post_names"),
     *   @Apidoc\Returned(ref="app\common\model\system\UserModel\getRoleNamesAttr", field="role_names"),
     * })
     */
    public function user()
    {
        $param = $this->params(['post_id/d' => '']);

        validate(PostValidate::class)->scene('user')->check($param);

        $where = $this->where(where_delete(['post_ids', 'in', [$param['post_id']]]));

        $data = PostService::user($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("职位用户解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("post_id", type="array", require=true, desc="职位id")
     * @Apidoc\Param("user_ids", type="array", require=false, desc="用户id，为空则解除所有用户")
     */
    public function userRemove()
    {
        $param = $this->params(['post_id/a' => [], 'user_ids/a' => []]);

        validate(PostValidate::class)->scene('userRemove')->check($param);

        $data = PostService::userRemove($param['post_id'], $param['user_ids']);

        return success($data);
    }
}
