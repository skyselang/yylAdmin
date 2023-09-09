<?php
namespace {$service.namespace};

use app\model\{$tables[0].model_name} as {$tables[0].model_name}Model;
use app\model\{$tables[1].model_name} as {$tables[1].model_name}Model;

class {$service.class_name}
{
    protected $model;

    public function __construct()
    {
        $this->model = new {$tables[0].model_name}Model();
    }
    /**
     * 查询分页数据
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function getPageList($page,$limit,$where=[]){
        $res = $this->model->where($where)
        ->withoutField('delete_time')
        ->order("id desc")
        ->paginate(['page' => $page,'list_rows'=> $limit])
        ->toArray();
        return $res;
    }

    /**
    * 根据id查询明细
    * @param $id
    * @return array|\think\Model|null
    * @throws \think\db\exception\DataNotFoundException
    * @throws \think\db\exception\DbException
    * @throws \think\db\exception\ModelNotFoundException
    */
    public function getInfoById($id){
        $res = $this->model->where('id',$id)->find();
        return $res;
    }

    /**
    * 新增
    */
    public function add($params){
        $res = $this->model->create($params);
        return $res;
    }

    /**
    * 编辑
    * @param $params
    * @return bool
    */
    public function update($params){
        $res = $this->model->where(["id" => $params['id']])->field(true)->save($params);
        return $res;
    }

    public function delete($id){
        $info = $this->model->find($id);
        $res = $info->delete();
        if ($res){
            return true;
        }
        return false;
    }

    /**
    * 设置状态
    * @param $uid
    * @param $status
    * @return name
    */
    public function setStatus($uid,$status){
        $data=array(
            'id'=>$uid,
            'status'=>$status
        );
        return $this->update($data);
    }

    /**
    * 新增关联数据
    */
    public function add{$tables[1].model_name}($params){
        $res = (new {$tables[1].model_name}Model())->create($params);
        return $res;
    }
    /**
    * 根据主表id查询副表分页数据
    */
    public function get{$tables[1].model_name}By{$form.relation_field}($page,$limit,$params){
        $where=[
            ['{$form.relation_field}','=',$params['{$form.relation_field}']],
        ];
        $res = (new {$tables[1].model_name}Model())->where($where)
        ->order("id desc")
        ->paginate(['page' => $page,'list_rows'=> $limit])
        ->toArray();
        return $res;
    }
}