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
use app\common\service\member\MemberService;
use app\common\model\member\MemberModel;
use app\common\model\member\AttributesModel;

/**
 * 会员管理验证器
 */
class MemberValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'          => ['require', 'array'],
        'member_id'    => ['require'],
        'username'     => ['require', 'length' => '2,64'],
        'nickname'     => ['length' => '1,64'],
        'password'     => ['require', 'length' => '6,18'],
        'password_old' => ['require', 'checkPwdOld'],
        'password_new' => ['require', 'length' => '6,18'],
        'phone'        => ['mobile'],
        'email'        => ['email'],
        'import'       => ['require', 'array'],
        'captcha_code' => ['require'],
        'phone_code'   => ['require'],
    ];

    // 错误信息
    protected $message = [
        'username.require'     => '请输入用户名',
        'username.length'      => '用户名长度为2至64个字符',
        'nickname.length'      => '昵称长度为1至64个字符',
        'password.require'     => '请输入密码',
        'password.length'      => '密码长度为6至18个字符',
        'password_old.require' => '请输入旧密码',
        'password_new.require' => '请输入新密码',
        'password_new.length'  => '新密码长度为6至18个字符',
        'phone.require'        => '请输入手机号码',
        'phone.mobile'         => '请输入正确的手机号码',
        'email.require'        => '请输入邮箱地址',
        'email.email'          => '请输入正确的邮箱地址',
        'captcha_code.require' => '请输入验证码',
    ];

    // 验证场景
    protected $scene = [
        'info'                 => ['member_id'],
        'add'                  => ['nickname', 'username', 'password', 'phone', 'email'],
        'edit'                 => ['member_id', 'nickname', 'username', 'phone', 'email'],
        'dele'                 => ['ids'],
        'region'               => ['ids'],
        'edittag'              => ['ids'],
        'editgroup'            => ['ids'],
        'repwd'                => ['ids', 'password'],
        'super'                => ['ids'],
        'disable'              => ['ids'],
        'import'               => ['import'],
        'usernameRegister'     => ['nickname', 'username', 'password'],
        'phoneRegisterCaptcha' => ['mobile'],
        'phoneRegister'        => ['nickname', 'mobile', 'password'],
        'phoneLoginCaptcha'    => ['mobile'],
        'phoneLogin'           => ['mobile'],
        'phoneBindCaptcha'     => ['mobile'],
        'phoneBind'            => ['mobile', 'member_id', 'captcha_code'],
        'phoneBindMini'        => ['phone_code', 'member_id'],
        'emailRegisterCaptcha' => ['email'],
        'emailRegister'        => ['nickname', 'email', 'password'],
        'emailLoginCaptcha'    => ['email'],
        'emailLogin'           => ['email'],
        'emailBindCaptcha'     => ['email'],
        'emailBind'            => ['email', 'member_id', 'captcha_code'],
        'editpwd0'             => ['member_id', 'password_old', 'password_new'],
        'editpwd1'             => ['member_id', 'password_new'],
        'logout'               => ['member_id'],
    ];

    // 验证场景定义：后台添加
    protected function sceneAdd()
    {
        return $this->only(['nickname', 'username', 'password', 'phone', 'email'])
            ->append('username', ['checkUsernameExisted'])
            ->append('phone', ['checkPhoneExisted'])
            ->append('email', ['checkEmailExisted']);
    }

    // 验证场景定义：后台修改
    protected function sceneEdit()
    {
        return $this->only(['nickname', 'username', 'phone', 'email'])
            ->append('username', ['checkUsernameExisted'])
            ->append('phone', ['checkPhoneExisted'])
            ->append('email', ['checkEmailExisted']);
    }

    // 验证场景定义：后台删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkTagGroup');
    }

    // 验证场景定义：用户名注册
    protected function sceneUsernameRegister()
    {
        return $this->only(['nickname', 'username', 'password'])
            ->append('username', ['checkUsernameExisted']);
    }

    // 验证场景定义：手机注册验证码
    protected function scenePhoneRegisterCaptcha()
    {
        return $this->only(['phone'])
            ->append('phone', ['require', 'checkPhoneExisted']);
    }

    // 验证场景定义：手机注册
    protected function scenePhoneRegister()
    {
        return $this->only(['nickname', 'phone', 'password'])
            ->append('phone', ['require', 'checkPhoneExisted']);
    }

    // 验证场景定义：手机登录验证码
    protected function scenePhoneLoginCaptcha()
    {
        return $this->only(['phone'])
            ->append('phone', ['require', 'checkPhoneIsExist']);
    }

    // 验证场景定义：手机登录
    protected function scenePhoneLogin()
    {
        return $this->only(['phone'])
            ->append('phone', ['require', 'checkPhoneIsExist']);
    }

    // 验证场景定义：手机绑定验证码
    protected function scenePhoneBindCaptcha()
    {
        return $this->only(['phone'])
            ->append('phone', ['require', 'checkPhoneExisted']);
    }

    // 验证场景定义：手机绑定
    protected function scenePhoneBind()
    {
        return $this->only(['phone', 'member_id', 'captcha_code'])
            ->append('phone', ['require', 'checkPhoneExisted']);
    }

    // 验证场景定义：邮箱注册验证码
    protected function sceneEmailRegisterCaptcha()
    {
        return $this->only(['email'])
            ->append('email', ['require', 'checkEmailExisted']);
    }

    // 验证场景定义：邮箱注册
    protected function sceneEmailRegister()
    {
        return $this->only(['email', 'nickname', 'password'])
            ->append('email', ['require', 'checkEmailExisted']);
    }

    // 验证场景定义：邮箱登录验证码
    protected function sceneEmailLoginCaptcha()
    {
        return $this->only(['email'])
            ->append('email', ['require', 'checkEmailIsExist']);
    }

    // 验证场景定义：邮箱登录
    protected function sceneEmailLogin()
    {
        return $this->only(['email'])
            ->append('email', ['require', 'checkEmailIsExist']);
    }

    // 验证场景定义：邮箱绑定验证码
    protected function sceneEmailBindCaptcha()
    {
        return $this->only(['email'])
            ->append('email', ['require', 'checkEmailExisted']);
    }

    // 验证场景定义：邮箱绑定
    protected function sceneEmailBind()
    {
        return $this->only(['email', 'member_id', 'captcha_code'])
            ->append('email', ['require', 'checkEmailExisted']);
    }

    // 自定义验证规则：用户名是否已存在
    protected function checkUsernameExisted($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['username', '=', $data['username']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '用户名已存在：' . $data['username'];
        }

        return true;
    }

    // 自定义验证规则：用户名是否存在
    protected function checkUsernameIsExist($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['username', '=', $data['username']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if (empty($info)) {
            return '用户名不存在：' . $data['username'];
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhoneExisted($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['phone', '=', $data['phone']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '手机已存在：' . $data['phone'];
        }

        return true;
    }

    // 自定义验证规则：手机是否存在
    protected function checkPhoneIsExist($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['phone', '=', $data['phone']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if (empty($info)) {
            return '手机不存在：' . $data['phone'];
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmailExisted($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['email', '=', $data['email']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '邮箱已存在：' . $data['email'];
        }

        return true;
    }

    // 自定义验证规则：邮箱是否存在
    protected function checkEmailIsExist($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['email', '=', $data['email']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if (empty($info)) {
            return '邮箱不存在：' . $data['email'];
        }

        return true;
    }

    // 自定义验证规则：旧密码是否正确
    protected function checkPwdOld($value, $rule, $data = [])
    {
        $info = MemberService::info($data['member_id'] ?? 0);
        if (!password_verify($data['password_old'], $info['password'])) {
            return '旧密码错误';
        }

        return true;
    }

    // 自定义验证规则：会员是否存在标签或分组
    protected function checkTagGroup($value, $rule, $data = [])
    {
        // $info = AttributesModel::field('member_id')->where('member_id', 'in', $data['ids'])->where('tag_id', '>', 0)->find();
        // if ($info) {
        //     return '会员存在标签，请在[标签]解除后再删除：' . $info['member_id'];
        // }

        // $info = AttributesModel::field('member_id')->where('member_id', 'in', $data['ids'])->where('group_id', '>', 0)->find();
        // if ($info) {
        //     return '会员存在分组，请在[分组]解除后再删除：' . $info['member_id'];
        // }

        return true;
    }
}
