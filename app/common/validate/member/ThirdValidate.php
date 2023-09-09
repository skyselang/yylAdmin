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
use app\common\model\member\ThirdModel;
use app\common\model\member\MemberModel;

/**
 * 会员第三方账号验证器
 */
class ThirdValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'      => ['require', 'array'],
        'third_id' => ['require'],
        'openid'   => ['require', 'checkExisted'],
    ];

    // 错误信息
    protected $message = [
        'openid.require' => '请输入openid',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['third_id'],
        'add'     => ['openid'],
        'edit'    => ['third_id', 'openid'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
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
        $model = new ThirdModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['platform', '=', $data['platform']];
        $where[] = ['application', '=', $data['application']];
        if ($data['unionid'] ?? '') {
            $where[] = ['unionid', '=', $data['unionid']];
        } else {
            $where[] = ['openid', '=', $data['openid']];
        }
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '第三方账号已存在：' . $data['unionid'] ?? $data['openid'];
        }

        return true;
    }

    // 自定义验证规则：第三方账号删除验证
    protected function checkDele($value, $rule, $data = [])
    {
        $ThirdModel = new ThirdModel();
        $ThirdPk = $ThirdModel->getPk();

        $MemberModel = new MemberModel();
        $MemberPk = $MemberModel->getPk();

        $third_ids = $data['ids'];
        $member_ids = $ThirdModel->whereIn($ThirdPk, $third_ids)->column($MemberPk);
        foreach ($member_ids as $member_id) {
            $member = $MemberModel->field($MemberPk . ',password')->find($member_id);
            if ($member) {
                $third_where = [[$MemberPk, '=', $member_id], where_delete()];
                $third_count = $ThirdModel->where($third_where)->count();
                if (empty($member['password']) && $third_count == 1) {
                    return '无法删除，会员密码未设置且仅绑定了一个第三方账号：' . $member[$MemberPk];
                }
            }
        }

        return true;
    }
}
