<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;

class Upload extends Common
{
	public function one()
    {
    	$imageList = Db::name('image')->select();
    	
    	$this->assign('imageList',$imageList);
    	return $this->fetch();
    }

    public function more()
    {   
        $imageList = Db::name('image')->select();
        
        $this->assign('imageList',$imageList);
    	return $this->fetch();
    }

    /**
     * 文件上传
     * @return json 上传结果&文件信息
     */
    public function upload(){
        // 获取表单上传文件 
        $file = request()->file('file');

        // 移动到/static/upload/目录下
        $info = $file->move( './static/upload/');

        // 上传成功
        if ($info) {
            // 文件名处理
            $name = $info->getinfo('name');
            $nameArr = explode('.', $name);
            $name = $nameArr[0];

            // 文件路径处理，替换反斜杠
            $src = '/static/upload/'.str_replace('\\', '/', $info->getSaveName());

            // 上传文件信息
            $res['code'] = 0;
            $res['msg'] = '上传成功';
            $res['src'] = $src;
            $res['name'] = $name;

            // 保存文件信息到数据库
            $this->saveFileToDatabase($src, $name);
        } else {
            // 上传失败获取错误信息
            $res['code'] = 1;
            $res['msg'] = $file->getError();
        }

        // 返回上传文件信息
        return json($res);
    }

    /**
     * 保存文件信息到数据库
     * @param  string $src 文件路径
     * @param  string $name 文件名称
     * @return null
     */
    public function saveFileToDatabase($src = '', $name = '')
    {
        if ($src && $name) {
            $data['src'] = $src;
            $data['name'] = $name;
            $data['create_time'] = time();
            Db::name('image')->insert($data);
        }
    }

    /**
     * 文件删除
     * @param string $url 文件路径
     * @return json 删除结果
     */
    public function delete()
    {
        $url = request()->param('url/s');
        $delete = unlink('.'.$url);
        if ($delete) {
            $res['code'] = 0;
            $res['msg'] = '删除成功';

            $this->deleFileToDatabase($url);
        } else {
            $res['code'] = 1;
            $res['msg'] = '删除失败';
        }
        
        return json($res);
    }

    /**
     * 文件信息删除从数据库
     * @param  string $src 文件路径
     * @return Boolean
     */
    public function deleFileToDatabase($src = '')
    {
        if ($src) {
            $where['src'] = $src;
            $delete = Db::name('image')->where($where)->delete();
            if ($delete) {
            	return true;
            } else {
            	return false;
            }
        } else {
        	return false;
        }
    }

}
