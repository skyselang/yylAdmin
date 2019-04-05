<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\Session;

class Admin extends Common
{
    /**
     * 修改资料
     * @return json
     */
    public function info()
    {
        $res['code'] = 1;
    	$admin_id = Session::get('admin_id');

    	if ($admin_id) {
    		$admin = Db::name('admin')->where('admin_id', $admin_id)->find();
    	} else {
    		$this->error('登录信息获取失败，请重新登录！');
    	}

    	$is_ajax = $this->request->isAjax();
    	if ($is_ajax) {
            $admin_id = Session::get('admin_id');
            if ($admin_id) {
                $nickname = $this->request->param('nickname/s');
                $email = $this->request->param('email/s');

                if (empty($nickname)) {
                    $res['msg'] = '请输入昵称';
                } elseif (empty($email)) {
                    $res['msg'] = '请输入邮箱';
                } else {
                    
                    $data['nickname'] = $nickname;
                    $data['email'] = $email;
                    $data['update_time'] = date('Y-m-d H:i:s');

                    $update = Db::name('admin')->where('admin_id', $admin_id)->update($data);

                    if ($update) {
                        $res['code'] = 0;
                        $res['msg'] = '修改成功';
                    } else {
    
                        $res['msg'] = '修改失败';
                    }
                }
            } else {
                $res['msg'] = '修改失败';
            }
    		
    		return json($res);
    	}
    	
    	$this->assign('info',$admin);
    	return $this->fetch();
    }

    /**
     * 修改密码
     * @return json 
     */
    public function pwd()
    {
        if ($this->request->isAjax()) {
            $res['code'] = 1;
            $admin_id = Session::get('admin_id');
            if ($admin_id) {
                $oldpwd = $this->request->param('oldpwd/s');
                $newpwd = $this->request->param('newpwd/s');
                $newpwds = $this->request->param('newpwds/s');

                if (empty($oldpwd)) {
                    $res['msg'] = '请输入原密码';
                } elseif (empty($newpwd)) {
                    $res['msg'] = '请输入新密码';
                } elseif (empty($newpwds)) {
                    $res['msg'] = '请再次输入新密码';
                } elseif ($newpwd !== $newpwds) {
                    $res['msg'] = '请新密码与确认密码不一致';
                } else {
                    $where['admin_id'] = $admin_id;
                    $where['password'] = md5($oldpwd);

                    $oldpwd_check = Db::name('admin')->where($where)->find();

                    if ($oldpwd_check) {
                        $data['password'] = md5($newpwds);
                        $data['update_time'] = date('Y-m-d H:i:s');

                        $update = Db::name('admin')->where('admin_id', $admin_id)->update($data);

                        if ($update) {
                            $res['code'] = 0;
                            $res['msg'] = '修改成功';
                        } else {
                            $res['msg'] = '修改失败';
                        }
                    } else {
                        $res['msg'] = '原密码错误';
                    }
                }
            } else {
                $res['msg'] = '修改失败';
            }
            
            return json($res);
        }
    	return $this->fetch();
    }
}
