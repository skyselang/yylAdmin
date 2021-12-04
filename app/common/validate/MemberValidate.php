<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理验证器
namespace app\common\validate;

use think\Validate;
use app\common\service\MemberService;

class MemberValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'list'         => ['require', 'array'],
        'member_id'    => ['require'],
        'username'     => ['require', 'length' => '2,32', 'alphaDash', 'checkUsername'],
        'nickname'     => ['length' => '1,32', 'checkNickname'],
        'password'     => ['require', 'length' => '6,18', 'alphaNum'],
        'password_old' => ['require', 'checkPwdOld'],
        'password_new' => ['require', 'length' => '6,18', 'alphaNum'],
        'phone'        => ['mobile', 'checkPhone'],
        'email'        => ['email', 'checkEmail'],
        'avatar'       => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '102400'],
    ];

    // 错误信息
    protected $message = [
        'username.require'      => '请输入账号',
        'username.length'       => '账号长度为2至32个字符',
        'username.alphaDash'    => '账号由字母、数字、下划线、破折号组成',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'password.alphaNum'     => '密码只能为数字和字母',
        'password_old.require'  => '请输入旧密码',
        'password_new.require'  => '请输入新密码',
        'password_new.length'   => '新密码长度为6至18个字符',
        'password_new.alphaNum' => '新密码只能为数字和字母',
        'phone.mobile'          => '请输入正确的手机号码',
        'email.email'           => '请输入正确的邮箱地址',
        'avatar.require'        => '请选择图片',
        'avatar.file'           => '请选择图片文件',
        'avatar.image'          => '请选择图片格式文件',
        'avatar.fileExt'        => '请选择jpg、png格式图片',
        'avatar.fileSize'       => '请选择大小小于100kb图片',
    ];

    // 验证场景
    protected $scene = [
        'id'       => ['member_id'],
        'info'     => ['member_id'],
        'add'      => ['username', 'nickname', 'password', 'phone', 'email'],
        'edit'     => ['member_id', 'username', 'nickname', 'phone', 'email'],
        'dele'     => ['list'],
        'repwd'    => ['list', 'password'],
        'editpwd'  => ['member_id', 'password_old', 'password_new'],
        'editpwd1' => ['member_id', 'password_new'],
        'disable'  => ['list'],
        'region'   => ['list'],
        'avatar'   => ['avatar'],
        'register' => ['username', 'nickname', 'password', 'phone', 'email'],
        'login'    => ['username', 'password'],
        'logout'   => ['member_id'],
    ];

    // 验证场景定义：登录
    protected function scenelogin()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length', 'alphaNum', 'checkUsername'])
            ->remove('password', ['length', 'alphaNum']);
    }

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        if (isset($data['member_id'])) {
            $where[] = ['member_id', '<>', $data['member_id']];
        }

        $where[] = ['username', '=', $data['username']];
        $where[] = ['is_delete', '=', 0];
        $member = MemberService::list($where, 1, 1, [], 'member_id');
        if ($member['list']) {
            return '账号已存在：' . $data['username'];
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        if (isset($data['member_id'])) {
            $where[] = ['member_id', '<>', $data['member_id']];
        }

        $where[] = ['nickname', '=', $data['nickname']];
        $where[] = ['is_delete', '=', 0];
        $member = MemberService::list($where, 1, 1, [], 'member_id');
        if ($member['list']) {
            return '昵称已存在：' . $data['nickname'];
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        if (isset($data['member_id'])) {
            $where[] = ['member_id', '<>', $data['member_id']];
        }
        
        $where[] = ['phone', '=', $data['phone']];
        $where[] = ['is_delete', '=', 0];
        $member = MemberService::list($where, 1, 1, [], 'member_id');
        if ($member['list']) {
            return '手机已存在：' . $data['phone'];
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        if (isset($data['member_id'])) {
            $where[] = ['member_id', '<>', $data['member_id']];
        }
        
        $where[] = ['email', '=', $data['email']];
        $where[] = ['is_delete', '=', 0];
        $member = MemberService::list($where, 1, 1, [], 'member_id');
        if ($member['list']) {
            return '邮箱已存在：' . $data['email'];
        }

        return true;
    }

    // 自定义验证规则：旧密码是否正确
    protected function checkPwdOld($value, $rule, $data = [])
    {
        if (isset($data['member_id'])) {
            $member       = MemberService::info($data['member_id']);
            $password     = $member['password'];
            $password_old = md5($data['password_old']);
            if ($password != $password_old) {
                return '旧密码错误';
            }
        }

        return true;
    }
}
