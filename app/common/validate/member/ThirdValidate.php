<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\member;

use think\Validate;
use app\common\service\member\ThirdService as Service;
use app\common\model\member\ThirdModel as Model;
use app\common\model\member\MemberModel;
use app\common\service\member\SettingService;

/**
 * 会员第三方账号验证器
 */
class ThirdValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'field'       => ['require', 'checkUpdateField'],
        'third_id'    => ['require'],
        'member_id'   => ['require'],
        'application' => ['require'],
        'unionid'     => ['requireWithout:openid', 'checkExisted'],
        'openid'      => ['requireWithout:unionid', 'checkExisted'],
        'import_file' => ['require', 'file', 'fileExt' => 'xlsx'],
    ];

    // 错误信息
    protected $message = [
        'unionid.requireWithout' => '请输入unionid或openid',
        'openid.requireWithout' => '请输入unionid或openid',
        'import_file.require' => '请选择导入文件',
        'import_file.fileExt' => '只允许xlsx文件格式',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['third_id'],
        'add'     => ['application', 'member_id', 'unionid', 'openid'],
        'edit'    => ['third_id', 'application', 'member_id', 'unionid', 'openid'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
        'update'  => ['ids', 'field'],
        'import'  => ['import_file'],
    ];

    // 验证场景定义：第三方账号删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkDele');
    }

    // 自定义验证规则：第三方账号是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model   = $this->model();
        $pk      = $model->getPk();
        $id      = $data[$pk] ?? 0;
        $unionid = $data['unionid'] ?? '';
        $openid  = $data['openid'] ?? '';

        if (empty($unionid) && empty($openid)) {
            return lang('请输入 unionid 或 unionid');
        }

        $where[] = [$pk, '<>', $id];
        if ($unionid) {
            $platform = SettingService::platform($data['application']);
            $where[] = ['platform', '=', $platform];
            $where[] = ['unionid', '=', $data['unionid']];
        } else {
            $where[] = ['application', '=', $data['application']];
            $where[] = ['openid', '=', $data['openid']];
        }
        $where = where_delete($where);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('第三方账号已存在：') . ($unionid ?? $openid);
        }

        return true;
    }

    // 自定义验证规则：第三方账号批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    // 自定义验证规则：第三方账号删除验证
    protected function checkDele($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();

        $member_model = new MemberModel();
        $member_pk    = $member_model->getPk();
        $member_ids   = $model->whereIn($pk, $data['ids'])->column($member_pk);
        foreach ($member_ids as $member_id) {
            $member = $member_model->field($member_pk . ',password')->find($member_id);
            if ($member) {
                $where = [[$member_pk, '=', $member_id], where_delete()];
                $count = $model->where($where)->count();
                if (empty($member['password']) && $count == 1) {
                    return lang('无法删除，会员未设置密码且仅绑定了一个第三方账号：') . $member[$member_pk];
                }
            }
        }

        return true;
    }
}
