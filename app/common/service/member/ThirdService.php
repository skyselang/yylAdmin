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
use app\common\cache\member\ThirdCache as Cache;
use app\common\model\member\ThirdModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\ImportService;


/**
 * 会员第三方账号
 */
class ThirdService
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
        'third_id'      => '',
        'member_id/d'   => '',
        'application/s' => '',
        'unionid/s'     => '',
        'openid/s'      => '',
        'avatar_id/d'   => 0,
        'headimgurl/s'  => '',
        'nickname/s'    => '',
        'remark/s'      => '',
        'sort/d'        => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'application'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("platforms", type="array", desc="平台"),
     *   @Apidoc\Returned("applications", type="array", desc="应用"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps         = $exp ? where_exps() : [];
        $platforms    = SettingService::platforms('', true);
        $applications = SettingService::applications('', true);

        return ['exps' => $exps, 'platforms' => $platforms, 'applications' => $applications];
    }

    /**
     * 会员第三方账号列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="third_id,member_id,platform,application,headimgurl,nickname,is_disable,login_num,login_ip,login_region,login_time,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getMemberNicknameAttr"}, field="member_nickname"),
     *   @Apidoc\Returned(ref={Model::class,"getMemberUsernameAttr"}, field="member_username"),
     *   @Apidoc\Returned(ref={Model::class,"getPlatformNameAttr"}, field="platform_name"),
     *   @Apidoc\Returned(ref={Model::class,"getApplicationNameAttr"}, field="application_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',member_id,platform,application,avatar_id,headimgurl,nickname,is_disable,login_num,login_ip,login_region,login_time,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'member_id')) {
            $with[] = $hidden[] = 'member';
            $append = array_merge($append, ['member_nickname', 'member_username']);
        }
        if (strpos($field, 'platform')) {
            $append[] = 'platform_name';
        }
        if (strpos($field, 'application')) {
            $append[] = 'application_name';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $field = select_field($field, $field_no);

        $count = $pages = 0;
        if ($total) {
            $count = model_where($model->clone(), $where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 会员第三方账号信息
     * @param int  $id   第三方账号id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="third_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getMemberNicknameAttr"}, field="member_nickname")
     * @Apidoc\Returned(ref={Model::class,"getMemberUsernameAttr"}, field="member_username")
     * @Apidoc\Returned(ref={Model::class,"getPlatformNameAttr"}, field="platform_name")
     * @Apidoc\Returned(ref={Model::class,"getApplicationNameAttr"}, field="application_name")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('会员第三方账号：') . $id);
                }
                return [];
            }
            $info = $info->append(['platform_name', 'application_name', 'member_nickname', 'member_username', 'is_disable_name'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 会员第三方账号添加
     * @param array $param 第三方账号信息
     * @Apidoc\Param(ref={Model::class}, field="member_id,platform,application,unionid,openid,headimgurl,nickname,remark")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (isset($param['avatar_id']) && $param['avatar_id']) {
            $param['headimgurl'] = '';
        }
        $param['platform']    = SettingService::platform($param['application']);
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 会员第三方账号修改
     * @param int|array $ids   第三方账号id
     * @param array     $param 第三方账号信息
     * @Apidoc\Param(ref={Model::class}, field="third_id,member_id,platform,application,unionid,openid,headimgurl,nickname,remark")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        if (isset($param['avatar_id']) && $param['avatar_id']) {
            $param['headimgurl'] = '';
        }
        if (isset($param['application'])) {
            $param['platform'] = SettingService::platform($param['application']);
        }
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $param;
    }

    /**
     * 会员第三方账号删除
     * @param array $ids  第三方账号id
     * @param bool  $real 是否真实删除
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = update_softdele();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $update;
    }

    /**
     * 会员第三方账号是否禁用
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
     * 会员第三方账号批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'third_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 会员第三方账号导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'member_id', 'name' => lang('会员ID'), 'width' => 12],
            ['field' => 'member_nickname', 'name' => lang('会员昵称'), 'width' => 22, 'color' => ''],
            ['field' => 'member_username', 'name' => lang('会员用户名'), 'width' => 22],
            ['field' => 'platform_name', 'name' => lang('平台'), 'width' => 12],
            ['field' => 'application_name', 'name' => lang('应用'), 'width' => 12],
            ['field' => 'unionid', 'name' => 'unionid', 'width' => 12],
            ['field' => 'openid', 'name' => 'openid', 'width' => 12, 'color' => 'FF0000'],
            ['field' => 'nickname', 'name' => lang('昵称'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'login_time', 'name' => lang('登录时间'), 'width' => 22],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22],
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
     * 会员第三方账号导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_MEMBER_THIRD;

        $field = 'member_id,platform,application,avatar_id,headimgurl,nickname,is_disable,login_num,login_ip,login_region,login_time,create_time,update_time,unionid,openid,remark,sort';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 会员第三方账号导入
     * @param array $import_info 导入信息
     * @param bool  $is_add      是否添加导入信息
     * @Apidoc\Query(ref="importParam")
     * @Apidoc\Param(ref="importParam")
     * @Apidoc\Returned(ref="importParam")
     * @Apidoc\Returned(ref={ImportService::class,"info"})
     */
    public static function import($import_info, $is_add = false) {}
}
