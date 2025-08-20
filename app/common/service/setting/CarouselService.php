<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\setting\CarouselCache as Cache;
use app\common\model\setting\CarouselModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;

/**
 * 轮播管理
 */
class CarouselService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 添加修改字段
     */
    public static $editField = [
        'carousel_id' => '',
        'unique/s'    => '',
        'position/s'  => '',
        'file_id/d'   => 0,
        'title/s'     => '',
        'desc/s'      => '',
        'url/s'       => '',
        'remark/s'    => '',
        'sort/d'      => 250,
        'file_list/a' => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique', 'position', 'file_id'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps = $exp ? where_exps() : [];

        return ['exps' => $exps];
    }

    /**
     * 轮播列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="carousel_id,unique,file_id,title,position,desc,remark,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"file"}, field="file_url,file_name,file_ext,file_type,file_type_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',unique,file_id,title,position,desc,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'file_id')) {
            $with[] = $hidden[] = 'file';
            $append = array_merge($append, ['file_url', 'file_name', 'file_ext', 'file_type', 'file_type_name']);
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $pages = 0;
        if ($total) {
            $count = model_where($model->clone(), $where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 轮播信息
     * @param int|string $id   轮播id、编号
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="carousel_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"file"}, field="file_url,file_name,file_ext,file_type,file_type_name")
     * @Apidoc\Returned("file_list", ref="fileReturn", type="array", desc="文件列表")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('轮播不存在：') . $id);
                }
                return [];
            }
            $info = $info
                ->append(['file_url', 'file_name', 'file_ext', 'file_type', 'file_type_name', 'file_list', 'is_disable_name'])
                ->hidden(['file', 'files'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 轮播添加
     * @param array $param 轮播信息
     * @Apidoc\Param(ref={Model::class}, field="unique,position,file_id,title,link,desc,remark,sort")
     * @Apidoc\Param("file_list", ref="filesParam", type="array", desc="文件列表")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加文件列表
            if (isset($param['file_list'])) {
                $file_list_ids = file_ids($param['file_list']);
                $model->files()->saveAll($file_list_ids);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 轮播修改
     * @param int|array $ids   轮播id
     * @param array     $param 轮播信息
     * @Apidoc\Param(ref={Model::class}, field="carousel_id,unique,file_id,title,link,position,desc,sort")
     * @Apidoc\Param("file_list", ref="filesParam", type="array", desc="文件列表")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['file_list'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改文件列表
                    if (isset($param['file_list'])) {
                        $info = $info->append(['file_list_ids']);
                        model_relation_update($info, $info['file_list_ids'], file_ids($param['file_list']), 'files');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $param;
    }

    /**
     * 轮播删除
     * @param array $ids  轮播id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除文件列表
                    $info->files()->detach();
                }
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = update_softdele();
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $update;
    }

    /**
     * 轮播是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        return $data;
    }

    /**
     * 轮播批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 轮播导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $file_id    = $exp_imp == 'export' ? 'file_url' : 'file_id';
        $file_type  = $exp_imp == 'export' ? 'file_type_name' : 'file_type';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'unique', 'name' => lang('编号'), 'width' => 20],
            ['field' => $file_id, 'name' => lang('文件'), 'width' => 20, 'color' => 'FF0000'],
            ['field' => $file_type, 'name' => lang('类型'), 'width' => 12],
            ['field' => 'title', 'name' => lang('标题'), 'width' => 20],
            ['field' => 'url', 'name' => lang('链接'), 'width' => 20],
            ['field' => 'position', 'name' => lang('位置'), 'width' => 12],
            ['field' => 'desc', 'name' => lang('描述'), 'width' => 20],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22],
        ];
        // 生成下标
        foreach ($header as $index => &$value) {
            $value['index'] = $index;
        }
        if ($exp_imp == 'import') {
            $header[] = ['index' => -1, 'field' => 'result_msg', 'name' => lang('导入结果'), 'width' => 60];
        }

        return $header;
    }

    /**
     * 轮播导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SETTING_CAROUSEL;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }
}
