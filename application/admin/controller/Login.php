<?php

namespace app\admin\controller;

use think\Controller;

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

	public function verification()
	{
		$method = request()->method();

		if ($method == 'POST') {
			$username = request()->param('data/a');
			$password = request()->param('password/s');
			$verify = request()->param('verify/s');
			var_dump($_POST);
			if ($username == '' || $username == null || $username == 'undefined') {
				$res['rescode'] = -1;
				$res['message'] = '请输入账号！';
			} elseif ($password == '' || $password == null || $password == 'undefined') {
				$res['rescode'] = -1;
				$res['message'] = '请输入密码！';
			} elseif ($verify == '' || $verify == null || $verify == 'undefined') {
				$res['rescode'] = -1;
				$res['message'] = '请输入验证码！';
			} elseif (captcha_check($verify)) {
				$res['rescode'] = -1; 
				$res['message'] = '验证码错误！';
			} else {
				$admin = true;
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

			return $res;
		}
	}

	/**
	 * 退出系统
	 * @return
	 */
	public function loginout()
	{
		session(null);
		$this->success('退出成功！',url('login/login'));
	}
}
