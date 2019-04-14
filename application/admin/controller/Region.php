<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\Session;
use think\facade\Request;


class Region extends Common
{
	/**
	 * 地区列表
	 * @return json list
	 */
    public function region()
    {
    	if (Request::isAjax()) {
            $where['is_delete'] = 0;
            // 数据
            $data = Db::name('region')->where($where)->order('region_sort desc,region_id asc')->select(); 
            // 总记录数
            $count = Db::name('region')->where($where)->count();

            $res['code'] = 0;
            $res['data'] = $data;
            $res['count'] = $count;

    		return json($res);
    	}

        return $this->fetch();
    }

    /**
     * 地区添加
     * @return json 添加结果
     */
    public function region_add()
    {
    	if (Request::isAjax()) {
    		$res['code'] = 1;

    		$region_pid = Request::param('region_pid',0);
    		$region_name = Request::param('region_name');
    		$region_sort = Request::param('region_sort',200);

    		if (empty($region_name)) {
    			$res['msg'] = '请输入地区名称';
    		} else {
                $check = Db::name('region')->where(['region_pid'=>$region_pid,'region_name'=>$region_name,'is_delete'=>0])->find();
                if ($check) {
                    $res['msg'] = '地区已存在！';
                } else {
                    $data['region_pid'] = $region_pid;
                    $data['region_name'] = $region_name;
                    $data['region_sort'] = $region_sort;

                    $insert_id = Db::name('region')->insertGetId($data);
                    if ($insert_id) {
                        $region = Db::name('region')->where('region_id',$insert_id)->field('region_name,region_id,region_pid,region_sort')->find();

                        $res['code'] = 0;
                        $res['msg'] = '添加成功！';
                        $res['data'] = $region;
                    } else {
                        $res['msg'] = '添加失败！';
                    }
                }
    		}

    		return json($res);
    	}

        return $this->fetch();
    }

    /**
     * 地区编辑
     * @return json 编辑结果
     */
    public function region_edit()
    {
        if (Request::isAjax()) {
            $res['code'] = 1;

            $region_id = Request::param('region_id');
            $region_name = Request::param('region_name');
            $region_sort = Request::param('region_sort');

            if (empty($region_id)) {
                $res['msg'] = '编辑失败！';
            } else if (empty($region_name)) {
                $res['msg'] = '请输入地区名称';
            } elseif (empty($region_sort)) {
                $res['msg'] = '请输入地区排序';
            } else {
                $check = Db::name('region')->where('region_id','<>',$region_id)->where('region_name',$region_name)->find();
                if ($check) {
                    $res['msg'] = '该地区已存在！';
                } else {
                    $data['region_id'] = $region_id;
                    $data['region_name'] = $region_name;
                    $data['region_sort'] = $region_sort;

                    $update = Db::name('region')->update($data);
                    if ($update) {
                        $res['code'] = 0;
                        $res['msg'] = '编辑成功！';
                    } else {
                        $res['msg'] = '编辑失败！';
                    }
                }
            }

            return json($res);
        }

        $region_id = Request::param('region_id');
        $region = Db::name('region')->where('region_id',$region_id)->find();

        $this->assign('region',$region);

        return $this->fetch();
    }
    
    /**
     * 地区删除
     * @return json 删除结果
     */
    public function region_dele()
    {
        if (Request::isAjax()) {
            $res['code'] = 1;

            $region_id = Request::param('region_id');

            if (empty($region_id)) {
                $res['msg'] = '删除失败！';
            } else {
                $update = Db::name('region')->where('region_id',$region_id)->whereOr('region_pid',$region_id)->update(['is_delete'=>1]);
                if ($update) {
                    $res['code'] = 0;
                    $res['msg'] = '删除成功！';
                } else {
                    $res['msg'] = '删除失败！';
                }
            }

            return json($res);
        }
    }

}
