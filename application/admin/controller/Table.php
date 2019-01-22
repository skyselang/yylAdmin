<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Session;
use think\Db;

class Table extends Common
{
    public function table()
    {
        if ($this->request->isAJAX()) {
            // 分页
            $page = $this->request->param('page/s');
            $limit = $this->request->param('limit/s');

            // 条件
            $type = $this->request->param('type/s');
            $is_frame = $this->request->param('is_frame/s');
            $field = $this->request->param('field/s');
            $field_val = $this->request->param('field_val/s');
            $date_type = $this->request->param('date_type/s');
            $start_date = $this->request->param('start_date/s');
            $end_date = $this->request->param('end_date/s');
            if ($type) {
                $where[] = ['type', '=', $type];
            }
            if ($is_frame) {
                $where[] = ['is_frame', '=', $is_frame];
            }
            if ($field_val) {
                $where[] = [$field, 'like' ,'%'.$field_val.'%'];
            }
            if ($start_date && $end_date) {
                $start_time = strtotime($start_date.' 00:00:00');
                $end_time = strtotime($end_date.' 23:59:59');
                $where[] = [$date_type, ['>=', $start_time], ['<=', $end_time], 'and'];
            }
            $where[] = ['is_dele', '=', 1];//1正常-1删除

            // 排序
            $order_field = $this->request->param('order_field/s'); // 排序字段
            $order_type = $this->request->param('order_type/s'); // 排序方式
            if ($order_type) {
                $order = [$order_field=>$order_type];
            } else {
                $order = ['sort'=>'desc','id'=>'desc'];
            }

            // 数据
            $data = Db::name('news')
                ->where($where)
                ->field('id,image,title,author,keywords,description,type,look,sort,is_dele,is_frame,create_time,update_time')
                ->page($page,$limit)
                ->order($order)
                ->select(); 
            $count = Db::name('news')->where($where)->count();// 查询总记录数

            if ($data) {
                $type_arr = array('图文','链接','图集');//新闻类型
                foreach ($data as $k => $v) {
                    $data[$k]['image'] = '/static/img/logo.jpg';
                    $data[$k]['type'] = $type_arr[$v['type']-1];
                    $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
                    $data[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                }

                $res['code'] = 0;
                $res['count'] = $count;
                $res['data'] = $data;
            } else {
                $res['code'] = 0;
            }
            
            return json($res);
        }
    	
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    /**
     * 修改
     * @return json 修改结果
     */
    public function edit()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $field = $param['field'];
        $value = $param['value'];
        
        if ($id && $field && $value) {
            $update = Db::name('news')
                ->where('id', $id)
                ->update([$field => $value]);
            if ($update) {
                $res['code'] = 0;
                $res['msg'] = '修改成功';
            } else {
                $res['code'] = 1;
                $res['msg'] = '修改失败';
            }
        } else {
            $res['code'] = 1;
            $res['msg'] = '参数错误';
        }
        sleep(5);
        return json($res);
    }

    /**
     * 删除
     * @return json 删除结果
     */
    public function dele()
    {
        $is_ajax = $this->request->isAJAX();

        if ($is_ajax) {
            $id = $this->request->param('id/s');

            $strpos = strpos($id, ',');
            if ($strpos > 0) {
                $id_arr = explode(',', $id);
            } else {
                $id_arr = ['0'=>$id];
            }

            $success = $fail = 0;
            foreach ($id_arr as $k => $v) {
                $dele = Db::name('news')
                    ->where('id', $v)
                    ->update(['is_dele' => -1]);
                if ($dele) {
                    $success += 1;
                    Db::name('news')
                        ->where('id', $v)
                        ->update(['delete_time' => time()]);
                } else {
                    $fail += 1;
                }
            }

            $res['code'] = 0;
            $res['msg'] = "成功删除{$success}条，失败{$fail}条数据";
            sleep(5);
            return json($res);
        }
    }
}
