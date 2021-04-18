<?php
/*
 * @Description  : 会员管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-04-14
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;
use app\common\service\MemberService;

class MemberValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'member_id'    => ['require', 'checkMember'],
        'username'     => ['require', 'alphaDash', 'checkUsername', 'length' => '2,32'],
        'nickname'     => ['checkNickname', 'length' => '1,32'],
        'password'     => ['require', 'alphaNum', 'length' => '6,18'],
        'password_old' => ['require', 'checkPwdOld'],
        'password_new' => ['require', 'alphaNum', 'length' => '6,18'],
        'phone'        => ['mobile', 'checkPhone'],
        'email'        => ['email', 'checkEmail'],
        'avatar'       => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '102400'],
    ];

    // 错误信息
    protected $message = [
        'member_id.require'     => '缺少参数：会员id',
        'username.require'      => '请输入会员名',
        'username.length'       => '会员名长度为2至32个字符',
        'username.alphaDash'    => '会员名由字母、数字、下划线、破折号组成',
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
        'dele'     => ['member_id'],
        'pwd'      => ['member_id', 'password'],
        'editpwd'  => ['member_id', 'password_old', 'password_new'],
        'disable'  => ['member_id'],
        'avatar'   => ['member_id', 'avatar'],
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

    // 自定义验证规则：会员是否存在
    protected function checkMember($value, $rule, $data = [])
    {
        $member_id = $value;

        $member = MemberService::info($member_id);

        if ($member['is_delete'] == 1) {
            return '会员已被删除：' . $member_id;
        }

        return true;
    }

    // 自定义验证规则：会员名是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';
        $username  = $data['username'];

        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }
        $where[] = ['username', '=', $username];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        if ($member) {
            return '会员名已存在：' . $username;
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';
        $nickname  = $data['nickname'];

        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }
        $where[] = ['nickname', '=', $nickname];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        if ($member) {
            return '昵称已存在：' . $nickname;
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';
        $phone     = $data['phone'];

        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }
        $where[] = ['phone', '=', $phone];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        if ($member) {
            return '手机已存在：' . $phone;
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';
        $email     = $data['email'];

        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }
        $where[] = ['email', '=', $email];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        if ($member) {
            return '邮箱已存在：' . $email;
        }

        return true;
    }

    // 自定义验证规则：旧密码是否正确
    protected function checkPwdOld($value, $rule, $data = [])
    {
        $member_id    = isset($data['member_id']) ? $data['member_id'] : '';
        $member       = MemberService::info($member_id);
        $password     = $member['password'];
        $password_old = md5($data['password_old']);

        if ($password != $password_old) {
            return '旧密码错误';
        }

        return true;
    }
}
