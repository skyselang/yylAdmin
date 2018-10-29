<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;

class Login extends Controller
{
	/**
	 * 登录页面
	 * @return
	 */
	public function login()
	{
		return $this->fetch();	
	}

	/**
	 * 登录验证
	 * @return json 验证结果
	 */
	public function verification()
	{
		$username = $this->request->param('username/s');
		$password = $this->request->param('password/s');
		$verify = $this->request->param('verify/s');
		$device = $this->request->param('device/s');

		if ($username == '' || $username == null || $username == 'undefined') {
			$res['rescode'] = -1;
			$res['message'] = '请输入账号！';
		} elseif ($password == '' || $password == null || $password == 'undefined') {
			$res['rescode'] = -1;
			$res['message'] = '请输入密码！';
		} elseif ($verify == '' || $verify == null || $verify == 'undefined') {
			$res['rescode'] = -1;
			$res['message'] = '请输入验证码！';
		} elseif (!captcha_check($verify)) {
			$res['rescode'] = -1; 
			$res['message'] = '验证码错误！';
		} else {
			$admin = new Admin();
			dump($admin);die;
			$where['username'] = $username;
			$where['password'] = md5($password);
			$admin = Db::name('admin')->where($where)->find();
			if ($admin) {
				$where['username'] = $username;
				$where['password'] = $password;

				$res['rescode'] = 1;
				$res['message'] = '登录成功！';
			} else {
				$res['rescode'] = -1; 
				$res['message'] = '账号或密码错误！';
			}
		}

		return json($res);
	}

	/**
	 * 退出系统
	 * @return
	 */
	public function loginout()
	{
		session(null);
		$this->success('退出成功！',url('admin/login/login'));
	}
}
