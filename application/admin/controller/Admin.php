<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\Session;

class Admin extends Common
{
    public function info()
    {
    	$admin_id = Session::get('admin_id');
    	$table = Db::name('admin');

    	if ($admin_id) {
    		$admin = $table
    			->where('admin_id', $admin_id)
    			->find();
    	} else {
    		$this->error('登录信息获取失败，请重新登录！');
    	}

    	// 修改资料
    	$is_ajax = $this->request->isAjax();
    	if ($is_ajax) {
    		$nickname = $this->request->param('nickname/s');
    		$email = $this->request->param('email/s');

    		if ($nickname == '' || $nickname == null || $nickname == 'undefined') {
    			$res['code'] = 1;
    			$res['msg'] = '请输入昵称';
    		} elseif ($email == '' || $email == null || $email == 'undefined') {
    			$res['code'] = 1;
    			$res['msg'] = '请输入邮箱';
    		} else {
    			$data['nickname'] = $nickname;
    			$data['email'] = $email;
    			$data['create_time'] = time();

    			$update_res = $table
    				->where('admin_id', $admin_id)
    				->update($data);

    			if ($update_res) {
    				$res['code'] = 0;
    				$res['msg'] = '修改成功';
    			} else {
    				$res['code'] = 1;
    				$res['msg'] = '修改失败';
    			}
    		}
    		return json($res);
    	}
    	
    	$this->assign('info',$admin);
    	return $this->fetch();
    }

    public function password()
    {
    	return $this->fetch();
    }
}
