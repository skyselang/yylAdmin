<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\service\member\MemberService;
use app\common\service\member\TagService;
use app\common\service\member\GroupService;
use app\common\service\member\ThirdService;
use app\common\service\member\SettingService;
use app\common\service\setting\RegionService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员管理")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("100")
 */
class Member extends BaseController
{
    /**
     * @Apidoc\Title("会员列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="会员列表", children={
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,avatar_id,nickname,username,phone,email,sort,is_super,is_disable,create_time"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr", field="avatar_url"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getTagNamesAttr", field="tag_names"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getGroupNamesAttr", field="group_names"),
     * })
     * @Apidoc\Returned("genders", type="array", desc="性别")
     * @Apidoc\Returned("platforms", type="object", desc="平台")
     * @Apidoc\Returned("applications", type="object", desc="应用")
     * @Apidoc\Returned("tag", ref="app\common\model\member\TagModel", type="array", desc="标签列表", field="tag_id,tag_name")
     * @Apidoc\Returned("group", ref="app\common\model\member\GroupModel", type="array", desc="分组列表", field="group_id,group_name")
     * @Apidoc\Returned("region", ref="app\common\model\setting\RegionModel", type="tree", desc="地区树形", field="region_id,region_pid,region_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = MemberService::list($where, $this->page(), $this->limit(), $this->order());

        $data['tag']    = TagService::list([where_delete()], 0, 0, [], 'tag_id,tag_name')['list'] ?? [];
        $data['group']  = GroupService::list([where_delete()], 0, 0, [], 'group_id,group_name')['list'] ?? [];
        $data['region'] = RegionService::list('tree', [where_delete()], [], 'region_id,region_pid,region_name');
        $data['exps']   = where_exps();
        $data['where']  = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("会员信息")
     * @Apidoc\Query(ref="app\common\model\member\MemberModel", field="member_id")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getApplicationNameAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getPlatformNameAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getTagIdsAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getTagNamesAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getGroupIdsAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getGroupNamesAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getGenderNameAttr")
     */
    public function info()
    {
        $param = $this->params(['member_id/d' => '']);

        validate(MemberValidate::class)->scene('info')->check($param);

        $data = MemberService::info($param['member_id'], true, true, true);

        unset($data['password']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="avatar_id,nickname,username,password,phone,email,name,gender,region_id,remark,sort")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\getTagIdsAttr", field="tag_ids")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\getGroupIdsAttr", field="group_ids")
     */
    public function add()
    {
        $param = $this->params(MemberService::$edit_field);
        $param['password']    = $this->param('password');
        $param['platform']    = SettingService::PLATFORM_YA;
        $param['application'] = SettingService::APP_YA_ADMIN;

        validate(MemberValidate::class)->scene('add')->check($param);

        $data = MemberService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="member_id,avatar_id,nickname,username,phone,email,name,gender,region_id,remark,sort")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\getTagIdsAttr", field="tag_ids")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\getGroupIdsAttr", field="group_ids")
     */
    public function edit()
    {
        $param = $this->params(MemberService::$edit_field);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param['member_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("type", type="string", default="member", desc="member删除会员，third删除会员第三方账号")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => [], 'type/s' => 'member']);

        validate(MemberValidate::class)->scene('dele')->check($param);

        $type = $param['type'];
        if ($type == 'third') {
            $data = MemberService::thirdUnbind($param['ids'][0]);
        } else {
            $data = MemberService::dele($param['ids']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改所在地")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="region_id")
     */
    public function region()
    {
        $param = $this->params(['ids/a' => [], 'region_id/d' => 0]);

        validate(MemberValidate::class)->scene('region')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改标签")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\getTagIdsAttr", field="tag_ids")
     */
    public function edittag()
    {
        $param = $this->params(['ids/a' => [], 'tag_ids/a' => []]);

        validate(MemberValidate::class)->scene('edittag')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改分组")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\getGroupIdsAttr", field="group_ids")
     */
    public function editgroup()
    {
        $param = $this->params(['ids/a' => [], 'group_ids/a' => []]);

        validate(MemberValidate::class)->scene('editgroup')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="password")
     */
    public function repwd()
    {
        $param = $this->params(['ids/a' => [], 'password/s' => '']);

        validate(MemberValidate::class)->scene('repwd')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员是否超会")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="is_super")
     */
    public function super()
    {
        $param = $this->params(['ids/a' => [], 'is_super/d' => 0]);

        validate(MemberValidate::class)->scene('super')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="is_disable")
     * @Apidoc\Param("type", type="string", default="member", desc="member删除会员，third删除会员第三方账号")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0, 'type/s' => 'member']);

        validate(MemberValidate::class)->scene('disable')->check($param);

        $type = $param['type'];
        unset($param['type']);
        if ($type == 'third') {
            $data = ThirdService::edit($param['ids'], $param);
        } else {
            $data = MemberService::edit($param['ids'], $param);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("会员导入")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("import", type="array", desc="导入数据")
     */
    public function import()
    {
        $param = $this->params(['import/a' => []]);

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
                    'platform'    => SettingService::PLATFORM_YA,
                    'application' => SettingService::APP_YA_ADMIN,
                ];
                validate(MemberValidate::class)->scene('add')->check($add);
                MemberService::add($add);
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

        $data['import']  = $param['import'];
        $data['success'] = $success;
        $data['fail']    = $fail;

        $msg = '导入：' . count($param['import']) . '，成功：' . count($success) . '，失败：' . count($fail);

        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Method("GET")
     * @Apidoc\Query("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Query("date", type="array", default="", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("count", type="object", desc="数量统计", children={
     *   @Apidoc\Returned("name", type="string", desc="名称"),
     *   @Apidoc\Returned("date", type="string", desc="时间"),
     *   @Apidoc\Returned("count", type="string", desc="数量"),
     *   @Apidoc\Returned("title", type="string", desc="标题")
     * })
     * @Apidoc\Returned("echart", type="array", desc="图表数据", children={
     *   @Apidoc\Returned(ref="app\common\service\member\MemberService\statistic")
     * })
     */
    public function statistic()
    {
        $type = $this->param('type/s', '');
        $date = $this->param('date/a', []);

        $data['count'] = MemberService::statistic($type, $date, 'count');

        $field = ['number', 'application', 'platform'];
        foreach ($field as $v) {
            $echart[] = MemberService::statistic($type, $date, $v);
        }
        $data['echart'] = $echart ?? [];

        return success($data);
    }
}
