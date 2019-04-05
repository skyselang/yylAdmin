<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\Config;
use think\facade\Session;

class Login extends Controller {
	/**
	 * 登录页面
	 * @return
	 */
	public function index() {
		$this->redirect('admin/login/login');
	}
	/**
	 * 登录页面
	 * @return
	 */
	public function login() {
		$is_verify = config::get('app.is_verify');
		$this->assign('is_verify', $is_verify);
		return $this->fetch();
	}

	/**
	 * 登录验证
	 * @return json 验证结果
	 */
	public function check() 
	{
		$res['code'] = 1;

		$is_verify = config::get('app.is_verify'); //是否开启验证码
		$username = $this->request->param('username/s');
		$password = $this->request->param('password/s');
		$verify = $this->request->param('verify/s');
		$device = $this->request->param('device/s');
		$login_ip = $this->request->ip();

		if ($username == '' || $username == null || $username == 'undefined') {
			$res['msg'] = '请输入账号！';
		} elseif ($password == '' || $password == null || $password == 'undefined') {
			$res['msg'] = '请输入密码！';
		} elseif (($is_verify) && ($verify == '' || $verify == null || $verify == 'undefined')) {
			$res['msg'] = '请输入验证码！';
		} elseif (!captcha_check($verify) && $is_verify) {
			$res['msg'] = '验证码错误！';
		} else {
			$where['username'] = $username;
			$where['password'] = md5($password);

			$admin = Db::name('admin')
				->where($where)
				->find();

			if ($admin) {
				$admin_id = $admin['admin_id'];

				Session::set('admin_id', $admin_id);
				Session::set('username', $admin['username']);
				Session::set('nickname', $admin['nickname']);

				$this->update($admin_id, $login_ip, $device);

				$res['code'] = 0;
				$res['msg'] = '登录成功！';
				$res['url'] = url('admin/index/index');
			} else {
				$res['msg'] = '账号或密码错误！';
			}
		}

		return json($res);
	}

	/**
	 * 更新登录信息
	 * @param  string  $admin_id 账号id
	 * @param  string  $login_ip 登录ip
	 * @param  integer $device   登录环境
	 * @return null
	 */
	public function update($admin_id = '', $login_ip = '0.0.0.0', $device = 0) 
	{
		if ($admin_id) {
			$data['login_ip'] = $login_ip;
			$data['device'] = $device;
			$data['login_time'] = date('Y-m-d H:i:s');

			Db::name('admin')->where('admin_id', $admin_id)->update($data);

			Db::name('admin')->where('admin_id', $admin_id)->setInc('login_num');
		}
	}

	/**
	 * 退出系统
	 * @return
	 */
	public function sysexit() {
		$admin_id = Session::get('admin_id');

		if ($admin_id) {
			$data['exit_time'] = date('Y-m-d H:i:s');
			Db::name('admin')->where('admin_id', $admin_id)->update($data);
		}

		Session::clear(); //清除session（当前作用域）
		echo "<script>parent.location.href='" . url('admin/login/login') . "'</script>"; //js父级页面跳转
	}
}
