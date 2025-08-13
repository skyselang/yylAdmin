<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\member\MemberCache as Cache;
use app\common\model\member\MemberModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\ImportService;
use app\common\service\file\FileService;
use app\common\service\setting\RegionService;
use app\common\model\member\GroupModel;
use app\common\model\member\TagModel;
use app\common\model\member\ThirdModel;
use app\common\model\member\AttributesModel;
use app\common\utils\ReturnCodeUtils;
use app\common\utils\Utils;
use think\facade\Db;

/**
 * 会员管理
 */
class MemberService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 添加修改字段
     */
    public static $editField = [
        'member_id'     => '',
        'unique/s'      => '',
        'avatar_id/d'   => 0,
        'nickname/s'    => '',
        'username/s'    => '',
        'phone/s'       => '',
        'email/s'       => '',
        'name/s'        => '',
        'gender/d'      => 0,
        'birthday'      => NULL,
        'hometown_id/d' => 0,
        'region_id/d'   => 0,
        'sort/d'        => 250,
        'remark/s'      => '',
        'is_super/d'    => 0,
        'tag_ids/a'     => [],
        'group_ids/a'   => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique', 'avatar_id', 'gender', 'region_id', 'tag_ids', 'group_ids'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("tags", ref={TagService::class,"info"}, type="array", desc="标签", field="tag_id,tag_name"),
     *   @Apidoc\Returned("groups", ref={GroupService::class,"info"}, type="array", desc="分组", field="group_id,group_name"),
     *   @Apidoc\Returned("regions", ref={RegionService::class,"info"}, type="tree", desc="地区", field="region_id,region_pid,region_name"),
     *   @Apidoc\Returned("api_ids", type="array", desc="接口id"),
     *   @Apidoc\Returned("genders", type="array", desc="性别"),
     *   @Apidoc\Returned("platforms", type="array", desc="平台"),
     *   @Apidoc\Returned("applications", type="array", desc="应用"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps         = $exp ? where_exps() : [];
        $tags         = TagService::list([where_delete()], 0, 0, [], 'tag_name', false)['list'] ?? [];
        $groups       = GroupService::list([where_delete()], 0, 0, [], 'group_name', false)['list'] ?? [];
        $regions      = RegionService::list('tree', [where_delete()], [], 'region_name');
        $api_ids      = array_column(ApiService::list('list', [where_delete()], [], 'api_name'), 'api_id');
        $genders      = SettingService::genders('', true);
        $platforms    = SettingService::platforms('', true);
        $applications = SettingService::applications('', true);

        return [
            'exps'         => $exps,
            'tags'         => $tags,
            'groups'       => $groups,
            'regions'      => $regions,
            'api_ids'      => $api_ids,
            'genders'      => $genders,
            'platforms'    => $platforms,
            'applications' => $applications,
        ];
    }

    /**
     * 会员列表
     * @param array  $where 条件
     * @param int    $page  分页
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="member_id,avatar_id,nickname,username,phone,email,sort,is_super,is_disable,create_time"),
     *   @Apidoc\Returned(ref={Model::class,"getAvatarUrlAttr"}, field="avatar_url"),
     *   @Apidoc\Returned(ref={Model::class,"getTagNamesAttr"}, field="tag_names"),
     *   @Apidoc\Returned(ref={Model::class,"getGroupNamesAttr"}, field="group_names"),
     *   @Apidoc\Returned(ref={Model::class,"getIsSuperNameAttr"}, field="is_super_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = [$group => 'desc'];
        }
        if (empty($field)) {
            $field = $group . ',avatar_id,nickname,username,phone,email,sort,is_super,is_disable,create_time';
        } else {
            $field = $group . ',' . $field;
        }

        $wt = 'member_attributes ';
        $wa = 'b';
        $model = $model->alias('a');
        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'tag_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.member_id=' . $wa . '.member_id');
                $where[$wk] = [$wa . '.tag_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'tag_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.member_id=' . $wa . '.member_id');
                $where_scope[] = [$wa . '.tag_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === 'group_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.member_id=' . $wa . '.member_id');
                $where[$wk] = [$wa . '.group_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'group_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.member_id=' . $wa . '.member_id');
                $where_scope[] = [$wa . '.group_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === $pk) {
                $where[$wk] = ['a.' . $wv[0], $wv[1], $wv[2]];
            }
        }
        $where = array_values($where);

        $with     = ['tags', 'groups'];
        $append   = ['tag_names', 'group_names'];
        $hidden   = ['tags', 'groups'];
        $field_no = [];
        if (strpos($field, 'avatar_id')) {
            $with[]   = $hidden[] = 'avatar';
            $append[] = 'avatar_url';
            $field .= ',headimgurl';
        }
        if (strpos($field, 'is_super')) {
            $append[] = 'is_super_name';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $pages = 0;
        if ($total) {
            $count = model_where(clone $model, $where, $where_scope)->group($group)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $pages = ceil($count / $limit);
            $model = $model->limit($limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where, $where_scope);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->group($group)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 会员信息
     * @param int  $id    会员id
     * @param bool $exce  不存在是否抛出异常
     * @param bool $group 是否返回分组信息
     * @param bool $third 是否返回第三方账号信息
     * @param string $field_no 排除字段
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="member_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getAvatarUrlAttr"}, field="avatar_url")
     * @Apidoc\Returned(ref={Model::class,"getGenderNameAttr"}, field="gender_name")
     * @Apidoc\Returned(ref={Model::class,"getAgeAttr"}, field="age")
     * @Apidoc\Returned(ref={Model::class,"getHometownNameAttr"}, field="hometown_name")
     * @Apidoc\Returned(ref={Model::class,"getRegionNameAttr"}, field="region_name")
     * @Apidoc\Returned(ref={Model::class,"getPlatformNameAttr"}, field="platform_name")
     * @Apidoc\Returned(ref={Model::class,"getApplicationNameAttr"}, field="application_name")
     * @Apidoc\Returned(ref={Model::class,"getIsSuperNameAttr"}, field="is_super_name")
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Returned(ref={Model::class,"getTagNamesAttr"}, field="tag_names")
     * @Apidoc\Returned(ref={Model::class,"getGroupIdsAttr"}, field="group_ids")
     * @Apidoc\Returned(ref={Model::class,"getGroupNamesAttr"}, field="group_names")
     * @Apidoc\Returned("api_ids", type="array", desc="接口id")
     * @Apidoc\Returned("api_urls", type="array", desc="接口url")
     * @Apidoc\Returned("api_list", type="array", desc="接口列表", ref={ApiService::class,"info"}, field="api_id,api_pid,api_name,api_url,is_unlogin,is_unauth")
     * @Apidoc\Returned("thirds", type="array", desc="第三方账号", ref={ThirdService::class,"info"}, field="member_id,platform,application,headimgurl,nickname,is_disable,login_time,create_time")
     */
    public static function info($id, $exce = true, $group = false, $third = false, $field_no = '')
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->with(['avatar', 'tags', 'groups'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('会员不存在：') . $id);
                }
                return [];
            }
            $info = $info
                ->append(['avatar_url', 'gender_name', 'age', 'platform_name', 'application_name', 'is_super_name', 'is_disable_name', 'tag_ids', 'tag_names', 'group_ids', 'group_names', 'hometown_name', 'region_name'])
                ->hidden(['avatar', 'tags', 'groups'])
                ->toArray();

            // 0原密码修改密码，1直接设置新密码
            $info['pwd_edit_type'] = 0;
            if (empty($info['password'])) {
                $info['pwd_edit_type'] = 1;
            }


            if (member_is_super($id)) {
                $api_list = ApiService::list('list', [where_delete()], [], 'api_url');
                $api_ids  = array_column($api_list, 'api_id');
                $api_urls = array_column($api_list, 'api_url');
            } elseif ($info['is_super']) {
                $api_list = ApiService::list('list', where_disdel(), [], 'api_url');
                $api_ids  = array_column($api_list, 'api_id');
                $api_urls = array_column($api_list, 'api_url');
            } else {
                $group_api_ids  = GroupService::apiIds($info['group_ids'], where_disdel());
                $unauth_api_ids = ApiService::unauthList('id');
                $api_ids        = array_merge($group_api_ids, $unauth_api_ids);
                $api_list       = ApiService::list('list', where_disdel(['api_id', 'in', $api_ids]), [], 'api_url');
                $api_urls       = array_column($api_list, 'api_url');
            }
            $api_ids  = array_values(array_filter($api_ids));
            $api_urls = array_values(array_filter($api_urls));
            sort($api_ids);
            sort($api_urls);
            $info['api_ids']  = $api_ids;
            $info['api_urls'] = $api_urls;

            $cache->set($id, $info);
        }

        // 分组（权限）
        if ($group) {
            $member_api_ids = $info['api_ids'] ?? [];
            $api_field      = 'api_id,api_pid,api_name,api_url,is_unlogin,is_unauth';
            $api_lists      = ApiService::list('list', [where_delete()], [], $api_field);
            foreach ($api_lists as &$val) {
                $val['is_check'] = 0;
                $val['is_group'] = 0;
                foreach ($member_api_ids as $m_api_id) {
                    if ($val['api_id'] == $m_api_id) {
                        $val['is_check'] = 1;
                    }
                }
            }
            $info['api_list'] = list_to_tree($api_lists, 'api_id', 'api_pid');
        }

        // 第三方账号
        if ($third) {
            $info['thirds'] = self::thirdList($id);
        }

        if ($field_no) {
            $field_no = explode(',', $field_no);
            foreach ($field_no as $val) {
                $val = trim($val);
                unset($info[$val]);
            }
        }

        return $info;
    }

    /**
     * 会员添加
     * @param array $param 会员信息
     * @Apidoc\Param(ref={Model::class}, field="avatar_id,nickname,username,password,phone,email,name,gender,birthday,hometown_id,region_id,remark,sort")
     * @Apidoc\Param(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Param(ref={Model::class,"getGroupIdsAttr"}, field="group_ids")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 密码
        if (isset($param['password'])) {
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }
        // 默认分组
        if (empty($param['group_ids'])) {
            $param['group_ids'] = GroupService::defaultIds();
        }

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加标签
            if (isset($param['tag_ids'])) {
                $model->tags()->saveAll($param['tag_ids']);
            }
            // 添加分组
            if (isset($param['group_ids'])) {
                $model->groups()->saveAll($param['group_ids']);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 会员修改
     * @param int|array $ids   会员id
     * @param array     $param 会员信息
     * @Apidoc\Query(ref={Model::class})
     * @Apidoc\Param(ref={Model::class}, field="member_id,avatar_id,nickname,username,password,phone,email,name,gender,birthday,hometown_id,region_id,remark,sort")
     * @Apidoc\Param(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Param(ref={Model::class,"getGroupIdsAttr"}, field="group_ids")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 密码
        if (isset($param['password'])) {
            $param['pwd_time'] = datetime();
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['tag_ids', 'group_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        $info = $info->append(['tag_ids']);
                        model_relation_update($info, $info['tag_ids'], $param['tag_ids'], 'tags');
                    }
                    // 修改分组
                    if (isset($param['group_ids'])) {
                        $info = $info->append(['group_ids']);
                        model_relation_update($info, $info['group_ids'], $param['group_ids'], 'groups');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $param;
    }

    /**
     * 会员删除
     * @param int|array $ids  会员id
     * @param bool      $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $ThirdModel = new ThirdModel();

        // 启动事务
        $model::startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    $info->tags()->detach(); // 删除标签
                    $info->groups()->detach(); // 删除分组
                }
                $ThirdModel->where($pk, 'in', $ids)->delete(); // 删除第三方账号
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = update_softdele();
                $ThirdModel->where($pk, 'in', $ids)->update($update);
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model::commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model::rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $update;
    }

    /**
     * 会员是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        return $data;
    }

    /**
     * 会员批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 会员修改密码
     * @param array  $ids      id
     * @param string $password 密码
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("password", type="string", require=true, desc="密码")
     */
    public static function editpwd($ids, $password)
    {
        $data = self::edit($ids, ['password' => $password]);

        return $data;
    }

    /**
     * 会员修改分组
     * @param array $ids       id
     * @param array $group_ids 分组id
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("group_ids", type="array", require=true, desc="分组id")
     */
    public static function editgroup($ids, $group_ids)
    {
        $data = self::edit($ids, ['group_ids' => $group_ids]);

        return $data;
    }

    /**
     * 会员修改超会
     * @param array $ids      id
     * @param int   $is_super 是否超会
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("is_super", type="int", require=true, desc="是否超会")
     */
    public static function editsuper($ids, $is_super)
    {
        $data = self::edit($ids, ['is_super' => $is_super]);

        return $data;
    }

    /**
     * 会员导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $avatar_id = $exp_imp == 'export' ? 'avatar_url' : 'avatar_id';
        $group_ids = $exp_imp == 'export' ? 'group_names' : 'group_ids';
        $tag_ids = $exp_imp == 'export' ? 'tag_names' : 'tag_ids';
        $is_super = $exp_imp == 'export' ? 'is_super_name' : 'is_super';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => 'ID', 'width' => 12],
            ['field' => $avatar_id, 'name' => '头像', 'width' => 16],
            ['field' => 'nickname', 'name' => '昵称', 'width' => 26],
            ['field' => 'username', 'name' => '用户名', 'width' => 26, 'color' => 'FF0000'],
            ['field' => 'phone', 'name' => '手机', 'width' => 14],
            ['field' => 'email', 'name' => '邮箱', 'width' => 30],
            ['field' => $group_ids, 'name' => '分组', 'width' => 20],
            ['field' => $tag_ids, 'name' => '标签', 'width' => 20],
            ['field' => $is_super, 'name' => '超会', 'width' => 10],
            ['field' => 'remark', 'name' => '备注', 'width' => 20],
            ['field' => $is_disable, 'name' => '禁用', 'width' => 10],
            ['field' => 'sort', 'name' => '排序', 'width' => 10],
            ['field' => 'create_time', 'name' => '注册时间', 'width' => 22],
            ['field' => 'update_time', 'name' => '修改时间', 'width' => 22],
            ['field' => 'password', 'name' => '密码', 'width' => 16],
        ];
        // 生成下标
        foreach ($header as $index => &$value) {
            $value['index'] = $index;
        }
        if ($exp_imp == 'import') {
            $header[] = ['index' => -1, 'field' => 'result_msg', 'name' => lang('导入结果'), 'width' => 60];
        }

        return $header;
    }

    /**
     * 会员导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_MEMBER;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 会员导入
     * @param array $import_info 导入信息
     * @param bool  $is_add      是否添加导入信息
     * @Apidoc\Query(ref="importParam")
     * @Apidoc\Param(ref="importParam")
     * @Apidoc\Returned(ref="importParam")
     * @Apidoc\Returned(ref={ImportService::class,"info"})
     */
    public static function import($import_info, $is_add = false)
    {
        if ($is_add) {
            $import_info['type'] = FileSettingService::EXPIMP_TYPE_MEMBER;
            $import_id = ImportService::add($import_info);
            $data = ImportService::imports($import_id, __CLASS__, __FUNCTION__);
            return $data;
        }

        $header = self::header('import');
        $import = ImportService::importsReader($header, $import_info['file_path']);
        $model = self::model();
        $table = $model->getTable();
        $pk = $model->getPk();
        $import_num = count($import);
        $success = $fail = [];
        $datetime = datetime();
        $batch_num = 10000;

        while (count($import) > 0) {
            $batchs = array_splice($import, 0, $batch_num);
            foreach ($batchs as $key => $val) {
                $temp = [];
                foreach ($header as $vh) {
                    if ($vh['index'] > -1) {
                        $temp[$vh['field']] = $val[$vh['index']] ?? '';
                    }
                }
                $batchs[$key] = $temp;
            }

            $ids = array_column($batchs, $pk);
            $usernames = array_column($batchs, 'username');
            $phones = array_column($batchs, 'phone');
            $emails = array_column($batchs, 'email');
            $ids_repeat = array_repeat($ids);
            $usernames_repeat = array_repeat($usernames);
            $phones_repeat = array_repeat($phones);
            $emails_repeat = array_repeat($emails);
            $ids = Db::table($table)->where($pk, '>', 0)->where($pk, 'in', $ids)->where('is_delete', 0)->column($pk);
            $usernames = Db::table($table)->where($pk, '>', 0)->where($pk, 'not in', $ids)->where('username', 'in', $usernames)
                ->where('is_delete', 0)->column('username');
            $phones = Db::table($table)->where($pk, '>', 0)->where($pk, 'not in', $ids)->where('phone', 'in', $phones)
                ->where('is_delete', 0)->column('phone');
            $emails = Db::table($table)->where($pk, '>', 0)->where($pk, 'not in', $ids)->where('email', 'in', $emails)
                ->where('is_delete', 0)->column('email');

            $updates = $inserts = [];
            foreach ($batchs as $batch) {
                $batch['result_msg'] = [];
                if ($batch[$pk]) {
                    if (filter_var($batch[$pk], FILTER_VALIDATE_INT) === false) {
                        $batch['result_msg'][] = lang('ID只能是整数');
                    } elseif (in_array($batch[$pk], $ids_repeat)) {
                        $batch['result_msg'][] = lang('ID重复');
                    } elseif (!$import_info['is_update'] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'][] = lang('ID已存在');
                    }
                }
                if ($batch['avatar_id']) {
                    if (!is_numeric($batch['avatar_id']) && filter_var($batch['avatar_id'], FILTER_VALIDATE_URL) === false) {
                        $batch['result_msg'][] = lang('头像必须是文件id或有效url');
                    }
                }
                if (mb_strlen($batch['nickname']) > 64) {
                    $batch['result_msg'][] = '昵称长度为1-64位';
                }
                if ($batch['username']) {
                    if (mb_strlen($batch['username']) < 2 || mb_strlen($batch['username']) > 64) {
                        $batch['result_msg'][] = '用户名长度为2-64位';
                    } elseif (in_array($batch['username'], $usernames_repeat)) {
                        $batch['result_msg'][] = '用户名重复';
                    } elseif (in_array($batch['username'], $usernames)) {
                        $batch['result_msg'][] = '用户名已存在';
                    }
                } else {
                    $batch['result_msg'][] = '用户名不能为空';
                }
                if ($batch['phone']) {
                    if (!preg_match('/^1[3-9]\d{9}$/', $batch['phone'])) {
                        $batch['result_msg'][] = '手机格式错误';
                    } elseif (in_array($batch['phone'], $phones_repeat)) {
                        $batch['result_msg'][] = '手机重复';
                    } elseif (in_array($batch['phone'], $phones)) {
                        $batch['result_msg'][] = '手机已存在';
                    }
                }
                if ($batch['email']) {
                    if (!filter_var($batch['email'], FILTER_VALIDATE_EMAIL)) {
                        $batch['result_msg'][] = '邮箱格式错误';
                    } elseif (in_array($batch['email'], $emails_repeat)) {
                        $batch['result_msg'][] = '邮箱重复';
                    } elseif (in_array($batch['email'], $emails)) {
                        $batch['result_msg'][] = '邮箱已存在';
                    }
                }
                if ($batch['create_time']) {
                    if (!strtotime($batch['create_time'])) {
                        $batch['result_msg'][] = lang('注册时间格式错误');
                    }
                }
                if ($batch['update_time']) {
                    if (!strtotime($batch['update_time'])) {
                        $batch['result_msg'][] = lang('修改时间格式错误');
                    }
                }
                if ($batch['password']) {
                    if (mb_strlen($batch['password']) < 6 || mb_strlen($batch['password']) > 18) {
                        $batch['result_msg'][] = '密码长度为6-18位';
                    }
                }

                if ($batch['result_msg']) {
                    $batch['result_msg'] = lang('失败：') . implode('，', $batch['result_msg']);
                    $fail[] = $batch;
                } else {
                    $batch['result_msg'] = lang('成功：');
                    $batch_tmp = $batch;
                    $batch_tmp['is_disable'] = (in_array($batch['is_disable'], ['1', lang('是')])) ? 1 : 0;
                    $batch_tmp['avatar_id'] = is_numeric($batch['avatar_id']) ? $batch['avatar_id'] : FileService::fileId($batch['avatar_id']);
                    $batch_tmp['group_ids'] = GroupService::nameId($batch['group_ids']);
                    $batch_tmp['tag_ids'] = TagService::nameId($batch['tag_ids']);
                    $batch_tmp['is_super'] = (in_array($batch['is_super'], ['1', lang('是')])) ? 1 : 0;
                    if ($batch_tmp['password']) {
                        $batch_tmp['password'] = password_hash($batch_tmp['password'], PASSWORD_BCRYPT);
                    }
                    if ($batch[$pk] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'] .= lang('修改');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? null : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? $datetime : $batch['update_time'];
                        $updates[] = $batch_tmp;
                    } else {
                        $batch['result_msg'] .= lang('添加');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? $datetime : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? null : $batch['update_time'];
                        unset($batch_tmp[$pk]);
                        $inserts[] = $batch_tmp;
                    }
                    $success[] = $batch;
                }
            }
            unset($batchs, $usernames, $phones, $emails);

            $attr_adds = [];
            if ($updates) {
                foreach ($updates as $key => $update) {
                    if ($update['group_ids']) {
                        foreach ($update['group_ids'] as $group_id) {
                            $attr_adds[] = [$pk => $update[$pk], 'group_id' => $group_id, 'tag_id' => 0];
                        }
                    }
                    if ($update['tag_ids']) {
                        foreach ($update['tag_ids'] as $tag_id) {
                            $attr_adds[] = [$pk => $update[$pk], 'group_id' => 0, 'tag_id' => $tag_id];
                        }
                    }
                    unset($update['group_ids'], $update['tag_ids']);
                    $updates[$key] = $update;
                }
            }
            if ($inserts) {
                foreach ($inserts as $key => $insert) {
                    $insert_tmp = $insert;
                    unset($insert_tmp['group_ids'], $insert_tmp['tag_ids']);
                    $id = Db::table($table)->insertGetId($insert_tmp);
                    if ($insert['group_ids']) {
                        foreach ($insert['group_ids'] as $group_id) {
                            $attr_adds[] = [$pk => $id, 'group_id' => $group_id, 'tag_id' => 0];
                        }
                    }
                    if ($insert['tag_ids']) {
                        foreach ($insert['tag_ids'] as $tag_id) {
                            $attr_adds[] = [$pk => $id, 'group_id' => 0, 'tag_id' => $tag_id];
                        }
                    }
                }
            }
            $batch_header = $header;
            foreach ($batch_header as $key => $val) {
                if (in_array($val['field'], ['group_ids', 'tag_ids'])) {
                    unset($batch_header[$key]);
                }
            }
            $attr_del_ids = array_column($updates, $pk);
            batch_update($model, $batch_header, $updates);
            self::deleGroupAttr($attr_del_ids);
            self::deleTagAttr($attr_del_ids);
            if ($attr_adds) {
                AttributesModel::insertAll($attr_adds);
            }
            if ($updates || $inserts) {
                $cache = self::cache();
                $cache->clear();
            }
            unset($updates, $inserts);
        }
        unset($import);

        return ['import_num' => $import_num, 'header' => $header, 'success' => $success, 'fail' => $fail];
    }

    /**
     * 会员登录
     * @param array  $param 登录信息
     * @param string $type  登录方式：username账号，phone手机，email邮箱，register注册
     */
    public static function login($param, $type = '')
    {
        $model = self::model();
        $pk    = $model->getPk();

        $account   = $param['account'] ?? '';
        $username  = $param['username'] ?? $account;
        $phone     = $param['phone'] ?? $account;
        $email     = $param['email'] ?? $account;
        $member_id = $param[$pk] ?? 0;
        $password  = $param['password'] ?? '';

        $where = [];
        if ($type === 'username') {
            // 通过用户名登录
            $where[] = ['username', '=', $username];
        } else if ($type === 'phone') {
            // 通过手机登录
            $where[] = ['phone', '=', $phone];
        } else if ($type === 'email') {
            // 通过邮箱登录
            $where[] = ['email', '=', $email];
        } else if ($type === 'register') {
            // 注册后登录
            $where[] = [$pk, '=', $member_id];
        } else {
            if (validate(['account' => 'mobile'], [], false, false)->check(['account' => $account])) {
                $where[] = ['phone', '=', $account];
            } else if (validate(['account' => 'email'], [], false, false)->check(['account' => $account])) {
                $where[] = ['email', '=', $account];
            } else {
                $where[] = ['username', '=', $account];
            }
        }
        $where[] = where_delete();

        $field  = $pk . ',username,nickname,phone,email,password,login_num,is_disable';
        $member = $model->field($field)->where($where)->find();
        if (empty($member)) {
            if (empty($type)) {
                $member = $model->field($field)->where('username|phone|email', $account)->where([where_delete()])->find();
            }
            if (empty($member)) {
                exception(lang('账号或密码错误'));
            }
        }
        if ($password) {
            if (!password_verify($password, $member['password'])) {
                exception(lang('账号或密码错误'));
            }
        }

        $member = $member->toArray();
        if ($member['is_disable'] == 1) {
            exception(lang('账号已被禁用'));
        }

        $ip_info   = Utils::ipInfo();
        $member_id = $member[$pk];

        // 登录信息
        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_num']    = $member['login_num'] + 1;
        $update['login_time']   = datetime();
        $model->where($pk, $member_id)->update($update);

        // 会员信息
        $cache = self::cache();
        $cache->del($member_id);
        $member = self::info($member_id);

        // 返回字段
        $data = self::loginField($member);

        return $data;
    }

    /**
     * 会员登录返回字段
     * @param array $member 会员信息
     */
    public static function loginField($member)
    {
        $data = [];
        $setting = SettingService::info();
        $token_name = $setting['token_name'];
        $data[$token_name] = self::token($member);
        $fields = ['member_id', 'avatar_id', 'avatar_url', 'nickname', 'username', 'login_ip', 'login_time', 'login_num'];
        foreach ($fields as $field) {
            if (isset($member[$field])) {
                $data[$field] = $member[$field];
            }
        }

        return $data;
    }

    /**
     * 会员token
     * @param array $member 会员信息
     */
    public static function token($member)
    {
        return TokenService::create($member);
    }

    /**
     * 会员退出
     * @param int $id 会员id
     */
    public static function logout($id)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $update['logout_time'] = datetime();

        $model->where($pk, $id)->update($update);

        $update[$pk] = $id;

        $cache = self::cache();
        $cache->del($id);

        return $update;
    }

    /**
     * 会员分组接口权限
     * @param array $info 会员信息
     */
    public static function groupApi($info)
    {
        $member_api_ids = $info['api_ids'] ?? [];
        $api_field      = 'api_id,api_pid,api_name,api_url,is_unlogin,is_unauth';
        $api_lists      = ApiService::list('list', [where_delete()], [], $api_field);
        foreach ($api_lists as &$val) {
            $val['is_check'] = 0;
            $val['is_group'] = 0;
            foreach ($member_api_ids as $m_api_id) {
                if ($val['api_id'] == $m_api_id) {
                    $val['is_check'] = 1;
                }
            }
        }
        $info['group_api_tree'] = list_to_tree($api_lists, 'api_id', 'api_pid');

        $unlogin_api_ids = ApiService::unloginList('id');
        $unauth_api_ids  = ApiService::unauthList('id');
        $auth_api_ids    = array_merge($member_api_ids, $unlogin_api_ids, $unauth_api_ids);
        $auth_api_where  = [['api_id', 'in', $auth_api_ids], where_disdel(), where_delete()];
        $auth_api_list   = ApiService::list('list', $auth_api_where, [], $api_field);
        $info['auth_api_list'] = $auth_api_list;
        $info['auth_api_urls'] = array_values(array_filter(array_column($auth_api_list, 'api_url')));
        sort($info['auth_api_urls']);

        return $info;
    }

    /**
     * 会员第三方账号列表
     * @param int $member_id 会员ID
     */
    public static function thirdList($member_id)
    {
        $MemberModel = self::model();
        $MemberPk = $MemberModel->getPk();
        $third_where[] = [$MemberPk, '=', $member_id];
        $third_where[] = where_delete();
        $third_field = 'member_id,platform,application,headimgurl,nickname,is_disable,login_time,create_time';
        return ThirdService::list($third_where, 0, 0, [], $third_field)['list'] ?? [];
    }

    /**
     * 会员第三方账号登录
     * @param array $third_info 第三方账号信息
     * platform，application，openid，headimgurl，nickname，unionid，register，avatar_id
     */
    public static function thirdLogin($third_info)
    {
        $register    = $third_info['register'] ?? 0;
        $unionid     = $third_info['unionid'] ?? '';
        $openid      = $third_info['openid'] ?? '';
        $platform    = $third_info['platform'];
        $application = $third_info['application'];
        $phone       = $third_info['phone'] ?? '';
        $setting     = SettingService::info();
        $ip_info     = Utils::ipInfo();
        $login_ip    = $ip_info['ip'];
        $datetime    = datetime();


        if (empty($openid)) {
            exception(lang('登录失败') . '：get openid fail');
        }

        $applications = [
            SettingService::APP_WX_MINIAPP,
            SettingService::APP_WX_OFFIACC,
            SettingService::APP_WX_WEBSITE,
            SettingService::APP_WX_MOBILE,
            SettingService::APP_QQ_MINIAPP,
            SettingService::APP_QQ_WEBSITE,
            SettingService::APP_QQ_MOBILE,
            SettingService::APP_WB_WEBSITE,
        ];
        if (!in_array($application, $applications)) {
            exception(lang('登录失败') . '：application absent ' . $application);
        }

        $ThirdModel = new ThirdModel();
        $ThirdPk    = $ThirdModel->getPk();

        $MemberModel = self::model();
        $MemberPk    = $MemberModel->getPk();

        $third_field = $ThirdPk . ',member_id,platform,application,openid,unionid,login_num,is_disable';
        if ($unionid) {
            $third_u_where = [['platform', '=', $platform], ['unionid', '=', $unionid], where_delete()];
            $third_unionid = $ThirdModel->field($third_field)->where($third_u_where)->find();
        }
        $third_o_where = [['application', '=', $application], ['openid', '=', $openid], where_delete()];
        $third_openid  = $ThirdModel->field($third_field)->where($third_o_where)->find();

        $errmsg_login    = lang('系统维护，无法登录');
        $errmsg_register = lang('系统维护，无法注册');
        if ($third_unionid ?? [] || $third_openid) {
            if ($application == SettingService::APP_WX_MINIAPP && !$setting['wx_miniapp_login']) {
                exception($errmsg_login . '：wx miniapp');
            } elseif ($application == SettingService::APP_WX_OFFIACC && !$setting['wx_offiacc_login']) {
                exception($errmsg_login . '：wx offiacc');
            } elseif ($application == SettingService::APP_WX_WEBSITE && !$setting['wx_website_login']) {
                exception($errmsg_login . '：wx website');
            } elseif ($application == SettingService::APP_WX_MOBILE && !$setting['wx_mobile_login']) {
                exception($errmsg_login . '：wx mobile');
            } elseif ($application == SettingService::APP_QQ_MINIAPP && !$setting['qq_miniapp_login']) {
                exception($errmsg_login . '：qq miniapp');
            } elseif ($application == SettingService::APP_QQ_WEBSITE && !$setting['qq_website_login']) {
                exception($errmsg_login . '：qq website');
            } elseif ($application == SettingService::APP_QQ_MOBILE && !$setting['qq_mobile_login']) {
                exception($errmsg_login . '：qq mobile');
            } elseif ($application == SettingService::APP_WB_WEBSITE && !$setting['wb_website_login']) {
                exception($errmsg_login . '：wb website');
            }
            $third_u_id = $third_unionid[$ThirdPk] ?? 0;
            $third_o_id = $third_openid[$ThirdPk] ?? 0;
            $member_id  = $third_unionid[$MemberPk] ?? $third_openid[$MemberPk] ?? 0;
            if ($third_unionid['is_disable'] ?? 0 || $third_openid['is_disable'] ?? 0) {
                exception($errmsg_login . '：' . lang('会员第三方账号已被禁用'));
            }
        } else {
            if ($application == SettingService::APP_WX_MINIAPP && !$setting['wx_miniapp_register']) {
                exception($errmsg_register . '：wx miniapp');
            } elseif ($application == SettingService::APP_WX_OFFIACC && !$setting['wx_offiacc_register']) {
                exception($errmsg_register . '：wx offiacc');
            } elseif ($application == SettingService::APP_WX_WEBSITE && !$setting['wx_website_register']) {
                exception($errmsg_register . '：wx website');
            } elseif ($application == SettingService::APP_WX_MOBILE && !$setting['wx_mobile_register']) {
                exception($errmsg_register . '：wx mobile');
            } elseif ($application == SettingService::APP_QQ_MINIAPP && !$setting['qq_miniapp_register']) {
                exception($errmsg_register . '：qq miniapp');
            } elseif ($application == SettingService::APP_QQ_WEBSITE && !$setting['qq_website_register']) {
                exception($errmsg_register . '：qq website');
            } elseif ($application == SettingService::APP_QQ_MOBILE && !$setting['qq_mobile_register']) {
                exception($errmsg_register . '：qq mobile');
            } elseif ($application == SettingService::APP_WB_WEBSITE && !$setting['wb_website_register']) {
                exception($errmsg_register . '：wb website');
            }
            $third_u_id = 0;
            $third_o_id = 0;
            $member_id  = 0;

            if ($register == 0) {
                exception(lang('未注册'), ReturnCodeUtils::THIRD_UNREGISTERED);
            }
        }

        // 启动事务
        $ThirdModel->startTrans();
        try {
            $member_field = $MemberPk . ',headimgurl,nickname,login_num';
            $member_where = where_delete([$MemberPk, '=', $member_id]);
            $member = $MemberModel->field($member_field)->where($member_where)->find();
            if ($member) {
                if (isset($third_info['headimgurl'])) {
                    $member_update['headimgurl'] = $third_info['headimgurl'];
                }
                if (empty($member['nickname']) && isset($third_info['nickname'])) {
                    $member_update['nickname'] = $third_info['nickname'];
                }
                if (isset($third_info['avatar_id'])) {
                    $member_update['avatar_id'] = $third_info['avatar_id'];
                }
                if ($phone) {
                    $member_update['phone'] = $phone;
                }
                $member_update['login_num']    = $member['login_num'] + 1;
                $member_update['login_ip']     = $login_ip;
                $member_update['login_time']   = $datetime;
                $member_update['login_region'] = $ip_info['region'];
                $MemberModel->where($MemberPk, $member_id)->update($member_update);
                // 登录日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, SettingService::LOG_TYPE_LOGIN);
            } else {
                $third_username = md5(uniqid('third', true));
                if ($application == SettingService::APP_WX_MINIAPP) {
                    $member_insert['username'] = 'wxminiapp' . $third_username;
                } elseif ($application == SettingService::APP_WX_OFFIACC) {
                    $member_insert['username'] = 'wxoffiacc' . $third_username;
                } elseif ($application == SettingService::APP_WX_WEBSITE) {
                    $member_insert['username'] = 'wxwebsite' . $third_username;
                } elseif ($application == SettingService::APP_WX_MOBILE) {
                    $member_insert['username'] = 'wxmobile' . $third_username;
                } elseif ($application == SettingService::APP_QQ_MINIAPP) {
                    $member_insert['username'] = 'qqminiapp' . $third_username;
                } elseif ($application == SettingService::APP_QQ_WEBSITE) {
                    $member_insert['username'] = 'qqwebsite' . $third_username;
                } elseif ($application == SettingService::APP_QQ_MOBILE) {
                    $member_insert['username'] = 'qqmobile' . $third_username;
                } elseif ($application == SettingService::APP_WB_WEBSITE) {
                    $member_insert['username'] = 'wbwebsite' . $third_username;
                }
                if (isset($third_info['headimgurl'])) {
                    $member_insert['headimgurl'] = $third_info['headimgurl'];
                }
                if (isset($third_info['nickname'])) {
                    $member_insert['nickname'] = $third_info['nickname'];
                } else {
                    $member_insert['nickname'] = $member_insert['username'] ?? $third_username;
                }
                if (isset($third_info['avatar_id'])) {
                    $member_update['avatar_id'] = $third_info['avatar_id'];
                }
                if ($phone) {
                    $member_insert['phone'] = $phone;
                }
                $member_insert['platform']     = $platform;
                $member_insert['application']  = $application;
                $member_insert['login_num']    = 1;
                $member_insert['login_ip']     = $login_ip;
                $member_insert['login_time']   = $datetime;
                $member_insert['login_region'] = $ip_info['region'];
                $member_add = self::add($member_insert);
                $member_id  = $member_add[$MemberPk];
                // 注册日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, SettingService::LOG_TYPE_REGISTER);
            }

            if ($phone) {
                $phone_where = [[$MemberPk, '<>', $member_id], ['phone', '=', $phone], where_delete()];
                $phone_exist = $MemberModel->field($MemberPk)->where($phone_where)->find();
                if ($phone_exist) {
                    return lang('手机已存在：') . $phone;
                }
            }

            $third_save['member_id']    = $member_id;
            $third_save['openid']       = $openid;
            $third_save['login_ip']     = $login_ip;
            $third_save['login_time']   = $datetime;
            $third_save['login_region'] = $ip_info['region'];
            if ($unionid) {
                $third_save['unionid'] = $unionid;
            }
            if (isset($third_info['headimgurl'])) {
                $third_save['headimgurl'] = $third_info['headimgurl'];
            }
            if (isset($third_info['nickname'])) {
                $third_save['nickname'] = $third_info['nickname'];
            }

            if ($third_u_id && $third_u_id != $third_o_id) {
                $ThirdModel->where($ThirdPk, $third_u_id)->update(['update_time' => $datetime]);
            }
            if ($third_o_id) {
                $third_o_update = $third_save;
                $third_o_update['login_num'] = $third_openid['login_num'] + 1;
                $ThirdModel->where($ThirdPk, $third_o_id)->update($third_o_update);
            } else {
                $third_o_insert = $third_save;
                $third_o_insert['login_num']    = 1;
                $third_o_insert['platform']     = $platform;
                $third_o_insert['application']  = $application;
                $third_o_insert['create_time']  = $datetime;
                $ThirdModel->save($third_o_insert);
                $third_o_id = $ThirdModel->$ThirdPk;
            }

            // 提交事务
            $ThirdModel->commit();
        } catch (\Exception $e) {
            $errmsg = lang('登录失败') . '：' . $e->getMessage();
            // 回滚事务
            $ThirdModel->rollback();
        }

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        // 会员信息
        $cache = self::cache();
        $cache->del($member_id);
        $member = self::info($member_id);
        $data   = self::loginField($member);
        $data['member_id'] = $member_id;
        $data['third_id']  = $third_o_id;

        return $data;
    }

    /**
     * 会员第三方账号绑定
     * @param array $third_info 第三方账号信息
     * member_id、openid、platform、application、headimgurl、nickname、unionid
     */
    public static function thirdBind($third_info)
    {
        $member_id   = $third_info['member_id'];
        $unionid     = $third_info['unionid'] ?? '';
        $openid      = $third_info['openid'] ?? '';
        $platform    = $third_info['platform'];
        $application = $third_info['application'];
        $setting     = SettingService::info();
        $datetime    = datetime();

        if (empty($member_id)) {
            exception(lang('绑定失败') . '：member_id is null');
        }
        if (empty($openid)) {
            exception(lang('绑定失败') . '：get openid fail');
        }

        $applications = [
            SettingService::APP_WX_MINIAPP,
            SettingService::APP_WX_OFFIACC,
            SettingService::APP_WX_WEBSITE,
            SettingService::APP_WX_MOBILE,
            SettingService::APP_QQ_MINIAPP,
            SettingService::APP_QQ_WEBSITE,
            SettingService::APP_QQ_MOBILE,
            SettingService::APP_WB_WEBSITE,
        ];
        if (!in_array($application, $applications)) {
            exception(lang('绑定失败') . '：application absent ' . $application);
        }

        $ThirdModel = new ThirdModel();
        $ThirdPk    = $ThirdModel->getPk();

        $MemberModel = self::model();
        $MemberPk    = $MemberModel->getPk();

        $third_field = $ThirdPk . ',member_id,platform,application,openid,unionid,login_num';
        if ($unionid) {
            $third_u_where = [['unionid', '=', $unionid], ['platform', '=', $platform], where_delete()];
            $third_unionid = $ThirdModel->field($third_field)->where($third_u_where)->find();
        }
        $third_o_where = [['openid', '=', $openid], ['application', '=', $application], where_delete()];
        $third_openid  = $ThirdModel->field($third_field)->where($third_o_where)->find();

        $errmsg_bind = lang('功能维护，无法绑定');
        if ($application == SettingService::APP_WX_MINIAPP && !$setting['wx_miniapp_bind']) {
            exception($errmsg_bind . '：wx miniapp');
        } elseif ($application == SettingService::APP_WX_OFFIACC && !$setting['wx_offiacc_bind']) {
            exception($errmsg_bind . '：wx offiacc');
        } elseif ($application == SettingService::APP_WX_WEBSITE && !$setting['wx_website_bind']) {
            exception($errmsg_bind . '：wx website');
        } elseif ($application == SettingService::APP_WX_MOBILE && !$setting['wx_mobile_bind']) {
            exception($errmsg_bind . '：wx mobile');
        } elseif ($application == SettingService::APP_QQ_MINIAPP && !$setting['qq_miniapp_bind']) {
            exception($errmsg_bind . '：qq miniapp');
        } elseif ($application == SettingService::APP_QQ_WEBSITE && !$setting['qq_website_bind']) {
            exception($errmsg_bind . '：qq website');
        } elseif ($application == SettingService::APP_QQ_MOBILE && !$setting['qq_mobile_bind']) {
            exception($errmsg_bind . '：qq mobile');
        } elseif ($application == SettingService::APP_WB_WEBSITE && !$setting['wb_website_bind']) {
            exception($errmsg_bind . '：wb website');
        }
        $third_u_id = $third_unionid[$ThirdPk] ?? 0;
        $third_o_id = $third_openid[$ThirdPk] ?? 0;
        $third_m_id = $third_unionid[$MemberPk] ?? $third_openid[$MemberPk] ?? 0;
        if ($third_m_id && $third_m_id != $member_id) {
            exception(lang('绑定失败，已被其它会员绑定'));
        }

        // 启动事务
        $ThirdModel->startTrans();
        try {
            $member_field = $MemberPk . ',headimgurl,nickname,login_num';
            $member_where = where_delete([$MemberPk, '=', $member_id]);
            $member = $MemberModel->field($member_field)->where($member_where)->find();
            if (isset($third_info['headimgurl'])) {
                $member_update['headimgurl'] = $third_info['headimgurl'];
            }
            if (empty($member['nickname']) && isset($third_info['nickname'])) {
                $member_update['nickname'] = $third_info['nickname'];
            }
            if ($member_update ?? []) {
                $MemberModel->where($MemberPk, $member_id)->update($member_update);
            }

            $third_save['member_id'] = $member_id;
            $third_save['openid']    = $openid;
            if ($unionid) {
                $third_save['unionid'] = $unionid;
            }
            if (isset($third_info['headimgurl'])) {
                $third_save['headimgurl'] = $third_info['headimgurl'];
            }
            if (isset($third_info['nickname'])) {
                $third_save['nickname'] = $third_info['nickname'];
            }

            if ($third_u_id && $third_u_id != $third_o_id) {
                $ThirdModel->where($ThirdPk, $third_u_id)->update(['update_time' => $datetime]);
            }
            if ($third_o_id) {
                $third_o_update = $third_save;
                $ThirdModel->where($ThirdPk, $third_o_id)->update($third_o_update);
            } else {
                $third_o_insert = $third_save;
                $third_o_insert['platform']    = $platform;
                $third_o_insert['application'] = $application;
                $third_o_insert['create_time'] = $datetime;
                $ThirdModel->save($third_o_insert);
                $third_o_id = $ThirdModel->$ThirdPk;
            }

            // 提交事务
            $ThirdModel->commit();
        } catch (\Exception $e) {
            $errmsg = lang('绑定失败') . '：' . $e->getMessage();
            // 回滚事务
            $ThirdModel->rollback();
        }

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        // 会员信息
        $token_name = $setting['token_name'];
        $data[$token_name] = member_token();
        $data['member_id'] = $member_id;
        $data['third_id']  = $third_o_id;

        return $data;
    }

    /**
     * 会员第三方账号解绑
     * @param int $third_id  第三方账号id
     * @param int $member_id 会员id
     */
    public static function thirdUnbind($third_id, $member_id = 0)
    {
        $ThirdModel = new ThirdModel();
        $ThirdPk = $ThirdModel->getPk();

        $MemberModel = self::model();
        $MemberPk = $MemberModel->getPk();

        $third = $ThirdModel->find($third_id);
        if (empty($third) || $third['is_delete']) {
            exception(lang('第三方账号不存在') . '：' . $third_id);
        }
        if ($member_id && $third[$MemberPk] != $member_id) {
            exception(lang('解绑失败，非本会员绑定') . '：' . $third_id);
        }
        if ($third['is_delete']) {
            exception(lang('第三方账号已解绑') . '：' . $third_id);
        }
        if (empty($member_id)) {
            $member_id = $third[$MemberPk];
        }

        $member = $MemberModel->find($member_id);
        $third_where = [where_delete(), [$MemberPk, '=', $third[$MemberPk]]];
        $third_count = $ThirdModel->where($third_where)->count();
        if (empty($member['password']) && $third_count == 1) {
            exception(lang('无法解绑，会员未设置密码且仅绑定了一个第三方账号'));
        }

        return $ThirdModel->where($ThirdPk, $third_id)->update(update_softdele());
    }

    /**
     * 会员统计
     * @param string $type 日期类型：day，month
     * @param array  $date 日期范围：[开始日期，结束日期]
     * @param string $stat 统计类型：count总计，number数量，platform平台，application应用
     * @return array
     * @Apidoc\Query("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Query("date", type="array", default="", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("count", type="object", desc="数量统计", children={
     *   @Apidoc\Returned("name", type="string", desc="名称"),
     *   @Apidoc\Returned("date", type="string", desc="时间"),
     *   @Apidoc\Returned("count", type="string", desc="数量"),
     *   @Apidoc\Returned("title", type="string", desc="标题"),
     * })
     * @Apidoc\Returned("echart", type="array", desc="图表数据", children={
     *   @Apidoc\Returned("type", type="string", desc="日期类型"),
     *   @Apidoc\Returned("date", type="array", desc="日期范围"),
     *   @Apidoc\Returned("title", type="string", desc="图表title.text"),
     *   @Apidoc\Returned("legend", type="array", desc="图表legend.data"),
     *   @Apidoc\Returned("xAxis", type="array", desc="图表xAxis.data"),
     *   @Apidoc\Returned("series", type="array", desc="图表series"),
     * })
     */
    public static function statistic($type = 'month', $date = [], $stat = 'count')
    {
        if (empty($date)) {
            $date = [];
            if ($type == 'day') {
                $date[0] = date('Y-m-d', strtotime('-29 days'));
                $date[1] = date('Y-m-d');
            } else {
                $date[0] = date('Y-m', strtotime('-11 months'));
                $date[1] = date('Y-m');
            }
        }
        $sta_date = $date[0];
        $end_date = $date[1];

        $cache = self::cache();
        $key = $type . $stat . $sta_date . '_' . $end_date . lang_get();
        $data = $cache->get($key);
        if (empty($data)) {
            $dates = [];
            if ($type == 'day') {
                $s_time = strtotime(date('Y-m-d', strtotime($sta_date)));
                $e_time = strtotime(date('Y-m-d', strtotime($end_date)));
                while ($s_time <= $e_time) {
                    $dates[] = date('Y-m-d', $s_time);
                    $s_time = strtotime('next day', $s_time);
                }

                $field = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
                $group = "date_format(create_time,'%Y-%m-%d')";
            } else {
                $s_time = strtotime(date('Y-m-01', strtotime($sta_date)));
                $e_time = strtotime(date('Y-m-01', strtotime($end_date)));
                while ($s_time <= $e_time) {
                    $dates[] = date('Y-m', $s_time);
                    $s_time = strtotime('next month', $s_time);
                }

                $sta_date = date('Y-m-01', strtotime($sta_date));
                $end_date = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($end_date)))));

                $field = "count(create_time) as num, date_format(create_time,'%Y-%m') as date";
                $group = "date_format(create_time,'%Y-%m')";
            }
            $where[] = ['member_id', '>', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
            $where[] = where_delete();

            $model = self::model();
            $pk    = $model->getPk();

            if ($stat == 'count') {
                $data = [
                    ['date' => 'total', 'name' => lang('总数'), 'title' => lang('总数'), 'count' => 0],
                    ['date' => 'online', 'name' => lang('在线'), 'title' => lang('数量'), 'count' => 0],
                    ['date' => 'today', 'name' => lang('今天'), 'title' => lang('新增'), 'count' => 0],
                    ['date' => 'yesterday', 'name' => lang('昨天'), 'title' => lang('新增'), 'count' => 0],
                    ['date' => 'thisweek', 'name' => lang('本周'), 'title' => lang('新增'), 'count' => 0],
                    ['date' => 'lastweek', 'name' => lang('上周'), 'title' => lang('新增'), 'count' => 0],
                    ['date' => 'thismonth', 'name' => lang('本月'), 'title' => lang('新增'), 'count' => 0],
                    ['date' => 'lastmonth', 'name' => lang('上月'), 'title' => lang('新增'), 'count' => 0],
                ];

                foreach ($data as $k => $v) {
                    $where = [];
                    $where[] = ['member_id', '>', 0];
                    if ($v['date'] == 'total') {
                        $where[] = [$pk, '>', 0];
                    } elseif ($v['date'] == 'online') {
                        $where[] = ['login_time', '>=', date('Y-m-d H:i:s', time() - 3600)];
                        $where[] = ['login_time', '<=', date('Y-m-d H:i:s')];
                    } else {
                        if ($v['date'] == 'yesterday') {
                            $sta_date = $end_date = date('Y-m-d', strtotime('-1 day'));
                        } elseif ($v['date'] == 'thisweek') {
                            $sta_date = date('Y-m-d', strtotime('this week'));
                            $end_date = date('Y-m-d', strtotime('last day next week +0 day'));
                        } elseif ($v['date'] == 'lastweek') {
                            $sta_date = date('Y-m-d', strtotime('last week'));
                            $end_date = date('Y-m-d', strtotime('last day last week +7 day'));
                        } elseif ($v['date'] == 'thismonth') {
                            $sta_date = date('Y-m-01');
                            $end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', strtotime('next month')))));
                        } elseif ($v['date'] == 'lastmonth') {
                            $sta_date = date('Y-m-01', strtotime('last month'));
                            $end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', time()))));
                        } else {
                            $sta_date = $end_date = date('Y-m-d');
                        }
                        $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
                        $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
                    }
                    $where[] = where_delete();
                    $data[$k]['count'] = $model->where($where)->count();
                }

                $cache->set($key, $data, 120);

                return $data;
            } elseif ($stat == 'number') {
                $data['title'] = lang('数量');
                $data['selected'] = [lang('总数') => false];
                $add = $total = [];
                // 新增会员
                $adds = $model->field($field)->where($where)->group($group)->select()->column('num', 'date');
                // 会员总数
                foreach ($dates as $k => $v) {
                    $add[$k] = $adds[$v] ?? 0;
                    if ($type == 'month') {
                        $e_t = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($v)))));
                    } else {
                        $e_t = $v;
                    }
                    $total[$k] = $model->where(where_delete(['create_time', '<=', $e_t . ' 23:59:59']))->count();
                }
                $series = [
                    ['name' => lang('总数'), 'type' => 'line', 'data' => $total, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => lang('新增'), 'type' => 'line', 'data' => $add, 'label' => ['show' => true, 'position' => 'top']],
                ];
            } elseif ($stat == 'application') {
                $data['title'] = lang('应用');
                $data['selected'] = [];
                $series = [];
                $applications = SettingService::applications();
                foreach ($applications as $k => $v) {
                    $series[] = ['name' => $v, 'application' => $k, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']];
                }
                foreach ($series as $k => $v) {
                    $series_data = $model
                        ->field($field)
                        ->where($where)
                        ->where('application', $v['application'])
                        ->group($group)
                        ->select()
                        ->column('num', 'date');
                    foreach ($dates as $kx => $vx) {
                        $series[$k]['data'][$kx] = $series_data[$vx] ?? 0;
                    }
                }
            } elseif ($stat == 'platform') {
                $data['title'] = lang('平台');
                $data['selected'] = [];
                $series = [];
                $platforms = SettingService::platforms();
                foreach ($platforms as $k => $v) {
                    $series[] = ['name' => $v, 'platform' => $k, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']];
                }
                foreach ($series as $k => $v) {
                    $series_data = $model
                        ->field($field)
                        ->where($where)
                        ->where('platform', $v['platform'])
                        ->group($group)
                        ->select()
                        ->column('num', 'date');
                    foreach ($dates as $kx => $vx) {
                        $series[$k]['data'][$kx] = $series_data[$vx] ?? 0;
                    }
                }
            }

            $legend = array_column($series, 'name');

            $data['type']   = $type;
            $data['date']   = $date;
            $data['legend'] = $legend;
            $data['xAxis']  = $dates;
            $data['series'] = $series;

            $cache->set($key, $data);
        }

        return $data;
    }

    /**
     * 删除关联分组
     * @param array $ids id
     * @return int
     */
    public static function deleGroupAttr($ids)
    {
        if (empty($ids)) {
            return 0;
        }
        $model       = self::model();
        $pk          = $model->getPk();
        $group_model = new GroupModel();
        $group_pk    = $group_model->getPk();
        $res         = AttributesModel::where($pk, 'in', $ids)->where($group_pk, '>', 0)->delete();
        return $res;
    }

    /**
     * 删除关联标签
     * @param array $ids id
     * @return int
     */
    public static function deleTagAttr($ids)
    {
        if (empty($ids)) {
            return 0;
        }
        $model     = self::model();
        $pk        = $model->getPk();
        $tag_model = new TagModel();
        $tag_pk    = $tag_model->getPk();
        $res       = AttributesModel::where($pk, 'in', $ids)->where($tag_pk, '>', 0)->delete();
        return $res;
    }

    /**
     * 注销账号
     * @param int    $member_id 会员id
     * @param string $password  密码
     */
    public static function cancel($member_id, $password)
    {
        $member = self::info($member_id);
        if (empty($member)) {
            exception(lang('会员不存在'));
        }

        // 验证密码
        if (!empty($member['password'])) {
            if (!password_verify($password, $member['password'])) {
                exception(lang('密码错误'));
            }
        }

        // 删除会员
        return self::dele($member_id);
    }
}
