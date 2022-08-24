<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use app\common\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\service\member\MemberService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员管理")
 * @Apidoc\Group("adminMember")
 * @Apidoc\Sort("210")
 */
class Member extends BaseController
{
    /**
     * @Apidoc\Title("会员列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\MemberModel\listReturn", type="array", desc="会员列表")
     * @Apidoc\Returned("region", ref="app\common\model\setting\RegionModel\treeReturn", type="tree", childrenField="children", desc="地区树形")
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'member_id,gender,region_id,reg_channel,reg_type,is_disable');

        $data = MemberService::list($where, $this->page(), $this->limit(), $this->order(), '', $this->isExtra());

        return success($data);
    }

    /**
     * @Apidoc\Title("会员信息")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\id")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\infoReturn")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\avatar_url")
     */
    public function info()
    {
        $param['member_id'] = $this->param('member_id/d', '');

        validate(MemberValidate::class)->scene('info')->check($param);

        $data = MemberService::info($param['member_id']);

        unset($data['password'], $data['token']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\addParam")
     * @Apidoc\Param("username", type="string", mock="@string('lower', 6, 12)")
     * @Apidoc\Param("nickname", type="string", mock="@ctitle(6, 12)")
     * @Apidoc\Param("password", type="string", mock="@string('lower', 6)")
     * @Apidoc\Param("phone", type="string", mock="@phone")
     * @Apidoc\Param("email", type="string", mock="@email")
     */
    public function add()
    {
        $param['avatar_id']   = $this->param('avatar_id/d', 0);
        $param['username']    = $this->param('username/s', '');
        $param['nickname']    = $this->param('nickname/s', '');
        $param['password']    = $this->param('password/s', '');
        $param['phone']       = $this->param('phone/s', '');
        $param['email']       = $this->param('email/s', '');
        $param['name']        = $this->param('name/s', '');
        $param['gender']      = $this->param('gender/d', 0);
        $param['region_id']   = $this->param('region_id/d', 0);
        $param['remark']      = $this->param('remark/s', '');
        $param['sort']        = $this->param('sort/d', 250);
        $param['reg_channel'] = 6;
        $param['reg_type']    = 6;

        validate(MemberValidate::class)->scene('add')->check($param);

        $data = MemberService::add($param, 'post');

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\editParam")
     */
    public function edit()
    {
        $param['member_id'] = $this->param('member_id/d', '');
        $param['avatar_id'] = $this->param('avatar_id/d', 0);
        $param['username']  = $this->param('username/s', '');
        $param['nickname']  = $this->param('nickname/s', '');
        $param['phone']     = $this->param('phone/s', '');
        $param['email']     = $this->param('email/s', '');
        $param['name']      = $this->param('name/s', '');
        $param['gender']    = $this->param('gender/d', 0);
        $param['region_id'] = $this->param('region_id/d', 0);
        $param['remark']    = $this->param('remark/s', '');
        $param['sort']      = $this->param('sort/d', 250);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param['member_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(MemberValidate::class)->scene('dele')->check($param);

        $data = MemberService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改所在地")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\id")
     */
    public function region()
    {
        $param['ids']       = $this->param('ids/a', '');
        $param['region_id'] = $this->param('region_id/d', 0);

        validate(MemberValidate::class)->scene('region')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\password")
     */
    public function repwd()
    {
        $param['ids']      = $this->param('ids/a', '');
        $param['password'] = $this->param('password/s', '');

        validate(MemberValidate::class)->scene('repwd')->check($param);

        $data = MemberService::edit($param['ids'], ['password' => md5($param['password'])]);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->param('ids/a', '');
        $param['is_disable'] = $this->param('is_disable/d', 0);

        validate(MemberValidate::class)->scene('disable')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员导入")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("import", type="array", desc="导入数据")
     */
    public function import()
    {
        $param['import'] = $this->param('import/a', '');

        validate(MemberValidate::class)->scene('import')->check($param);

        $success = $fail = [];
        foreach ($param['import'] as $v) {
            $errmsg = '';
            try {
                $add = [
                    'nickname'    => $v['昵称'] ?? '',
                    'username'    => $v['用户名'] ?? '',
                    'phone'       => $v['手机'] ?? '',
                    'email'       => $v['邮箱'] ?? '',
                    'password'    => $v['密码'] ?? '',
                    'reg_channel' => 6,
                    'reg_type'    => 6,
                ];
                validate(MemberValidate::class)->scene('add')->check($add);
            } catch (\Exception $e) {
                $errmsg = $e->getMessage();
            }
            if ($errmsg) {
                $v['errmsg'] = $errmsg;
                $fail[] = $v;
            } else {
                $v['errmsg'] = '导入成功';
                $success[] = $v;
            }
        }

        $data['success'] = $success;
        $data['fail']    = $fail;

        $msg = '导入：' . count($param['import']) . '，成功：' . count($success) . '，失败：' . count($fail);

        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("会员回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\MemberModel\listReturn", type="array", desc="会员列表")
     * @Apidoc\Returned("region", ref="app\common\model\setting\RegionModel\treeReturn", type="tree", childrenField="children", desc="地区树形")
     */
    public function recover()
    {
        $where = $this->where(['is_delete', '=', 1], 'member_id,gender,region_id,reg_channel,reg_type,is_disable');

        $data = MemberService::list($where, $this->page(), $this->limit(), $this->order(), '', $this->isExtra());

        return success($data);
    }

    /**
     * @Apidoc\Title("会员回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids']       = $this->param('ids/a', '');
        $param['is_delete'] = 0;

        validate(MemberValidate::class)->scene('recoverReco')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(MemberValidate::class)->scene('recoverDele')->check($param);

        $data = MemberService::dele($param['ids'], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Method("GET")
     * @Apidoc\Param("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Param("date", type="array", default="", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("count", type="object", desc="数量统计",
     *     @Apidoc\Returned("name", type="string", desc="名称"),
     *     @Apidoc\Returned("date", type="string", desc="时间"),
     *     @Apidoc\Returned("count", type="string", desc="数量"),
     *     @Apidoc\Returned("title", type="string", desc="title")
     * )
     * @Apidoc\Returned("echart", type="array", desc="图表数据",
     *     @Apidoc\Returned("type", type="string", desc="日期类型"),
     *     @Apidoc\Returned("date", type="array", desc="日期范围"),
     *     @Apidoc\Returned("title", type="string", desc="图表title.text"),
     *     @Apidoc\Returned("legend", type="array", desc="图表legend.data"),
     *     @Apidoc\Returned("xAxis", type="string", desc="图表xAxis.data"),
     *     @Apidoc\Returned("series", type="string", desc="图表series")
     * )
     */
    public function stat()
    {
        $type = $this->param('type/s', '');
        $date = $this->param('date/a', []);

        $data['count'] = MemberService::stat($type, $date, 'count');

        $stat = ['number', 'reg_channel', 'reg_type'];
        foreach ($stat as $v) {
            $echart[] = MemberService::stat($type, $date, $v);
        }
        $data['echart'] = $echart ?? [];

        return success($data);
    }
}
