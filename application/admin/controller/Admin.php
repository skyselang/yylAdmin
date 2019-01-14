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

    	if ($admin_id) {
    		$admin = Db::name('admin')
    			->where('admin_id', $admin_id)
    			->find();
    	} else {
    		$this->error('登录信息获取失败，请重新登录！');
    	}

    	// 修改资料
    	$is_ajax = $this->request->isAjax();
    	if ($is_ajax) {
            $admin_id = Session::get('admin_id');
            if ($admin_id) {
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
                    $data['update_time'] = time();

                    $update = Db::name('admin')
                        ->where('admin_id', $admin_id)
                        ->update($data);

                    if ($update) {
                        $res['code'] = 0;
                        $res['msg'] = '修改成功';
                    } else {
                        $res['code'] = 1;
                        $res['msg'] = '修改失败';
                    }
                }
            } else {
                $res['code'] = 1;
                $res['msg'] = '修改失败';
            }
    		
    		return json($res);
    	}
    	
    	$this->assign('info',$admin);
    	return $this->fetch();
    }

    public function pwd()
    {
        // 修改密码
        $is_ajax = $this->request->isAjax();
        if ($is_ajax) {
            $admin_id = Session::get('admin_id');
            if ($admin_id) {
                $oldpwd = $this->request->param('oldpwd/s');
                $newpwd = $this->request->param('newpwd/s');
                $newpwds = $this->request->param('newpwds/s');

                if ($oldpwd == '' || $oldpwd == null || $oldpwd == 'undefined') {
                    $res['code'] = 1;
                    $res['msg'] = '请输入原密码';
                } elseif ($newpwd == '' || $newpwd == null || $newpwd == 'undefined') {
                    $res['code'] = 1;
                    $res['msg'] = '请输入新密码';
                } elseif ($newpwds == '' || $newpwds == null || $newpwds == 'undefined') {
                    $res['code'] = 1;
                    $res['msg'] = '请再次输入新密码';
                } elseif ($newpwd !== $newpwds) {
                    $res['code'] = 1;
                    $res['msg'] = '请新密码与确认密码不一致';
                } else {
                    $where['admin_id'] = $admin_id;
                    $where['password'] = md5($oldpwd);

                    $oldpwd_check = Db::name('admin')
                        ->where($where)
                        ->find();

                    if ($oldpwd_check) {
                        $data['password'] = md5($newpwds);
                        $data['update_time'] = time();

                        $update = Db::name('admin')
                            ->where('admin_id', $admin_id)
                            ->update($data);

                        if ($update) {
                            $res['code'] = 0;
                            $res['msg'] = '修改成功';
                        } else {
                            $res['code'] = 1;
                            $res['msg'] = '修改失败';
                        }
                    } else {
                        $res['code'] = 1;
                        $res['msg'] = '原密码错误';
                    }
                }
            } else {
                $res['code'] = 1;
                $res['msg'] = '修改失败';
            }
            
            return json($res);
        }
    	return $this->fetch();
    }
}
