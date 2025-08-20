<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\content;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\content\TagCache as Cache;
use app\common\model\content\TagModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\ImportService;
use app\common\cache\content\ContentCache;
use app\common\model\content\ContentModel;
use app\common\model\content\AttributesModel;
use app\common\service\file\FileService;
use think\facade\Db;

/**
 * 内容标签
 */
class TagService
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
        'tag_id'        => '',
        'tag_unique/s'  => '',
        'tag_name/s'    => '',
        'image_id/d'    => 0,
        'title/s'       => '',
        'keywords/s'    => '',
        'description/s' => '',
        'remark/s'      => '',
        'sort/d'        => 250,
        'images/a'      => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'tag_unique', 'image_id'];

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
     * 内容标签列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="tag_id,tag_unique,tag_name,image_id,description,remark,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getImageUrlAttr"}, field="image_url"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
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
            $field = $pk . ',tag_unique,tag_name,image_id,description,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        if (strpos($field, 'image_id')) {
            $with[]   = $hidden[] = 'image';
            $append[] = 'image_url';
        }
        if (strpos($field, 'images')) {
            $with[]   = $hidden[]   = 'files';
            $append[] = $field_no[] = 'images';
        } elseif (strpos($field, 'image_urls')) {
            $with[]   = $hidden[]   = 'files';
            $append[] = $field_no[] = 'image_urls';
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
     * 内容标签信息
     * @param int|string $id   标签id、编号
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="tag_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getImageUrlAttr"})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"})
     * @Apidoc\Returned(ref="imagesReturn")
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
                $where[] = ['tag_unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('内容标签不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['image_url', 'image_urls', 'images', 'is_disable_name'])
                ->hidden(['image', 'files'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 内容标签添加
     * @param array $param 标签信息
     * @Apidoc\Param(ref={Model::class}, withoutField="tag_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref="imagesParam")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['tag_unique'] ?? '')) {
            $param['tag_unique'] = uniqids();
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加图片
            if (isset($param['images'])) {
                $file_ids = file_ids($param['images']);
                $model->files()->saveAll($file_ids);
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

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 内容标签修改
     * @param int|array $ids   标签id
     * @param array     $param 标签信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref="imagesParam")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['images'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改图片
                    if (isset($param['images'])) {
                        $info = $info->append(['image_ids']);
                        model_relation_update($info, $info['image_ids'], file_ids($param['images']), 'files');
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
        $cache->clear();

        return $param;
    }

    /**
     * 内容标签删除
     * @param array $ids  内容标签id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = update_softdele();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->clear();

        return $update;
    }

    /**
     * 内容标签是否禁用
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
     * 内容标签批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'tag_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 内容标签导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $image_id   = $exp_imp == 'export' ? 'image_url' : 'image_id';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'tag_unique', 'name' => lang('编号'), 'width' => 22],
            ['field' => $image_id, 'name' => lang('图片(id或url)'), 'width' => 16],
            ['field' => 'tag_name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'title', 'name' => lang('标题'), 'width' => 22],
            ['field' => 'keywords', 'name' => lang('关键词'), 'width' => 22],
            ['field' => 'description', 'name' => lang('描述'), 'width' => 30],
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
     * 内容标签导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_CONTENT_TAG;

        $field = 'tag_unique,image_id,tag_name,title,keywords,description,remark,sort,is_disable,create_time,update_time';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 内容标签导入
     * @param array $import_info 导入信息
     * @param bool  $is_add      是否添加导入信息
     * @Apidoc\Query(ref="importParam")
     * @Apidoc\Param(ref="importParam")
     * @Apidoc\Returned(ref="importParam")
     * @Apidoc\Returned(ref={ImportService::class,"info"})
     */
    public static function import($import_info, $is_add = false)
    {
        if ($is_add) {
            $import_info['type'] = FileSettingService::EXPIMP_TYPE_CONTENT_TAG;
            $import_id = ImportService::add($import_info);
            $data = ImportService::imports($import_id, __CLASS__, __FUNCTION__);
            return $data;
        }

        $header = self::header('import');
        $import = ImportService::importsReader($header, $import_info['file_path']);
        $model = self::model();
        $table = $model->getTable();
        $pk = $model->getPk();
        $import_num = count($import);
        $success = $fail = [];
        $datetime = datetime();
        $batch_num = 10000;

        while (count($import) > 0) {
            $batchs = array_splice($import, 0, $batch_num);
            foreach ($batchs as $key => $val) {
                $import_tmp = [];
                foreach ($header as $vh) {
                    if ($vh['index'] > -1) {
                        $import_tmp[$vh['field']] = $val[$vh['index']] ?? '';
                    }
                }
                $batchs[$key] = $import_tmp;
            }

            $ids = array_column($batchs, $pk);
            $uniques = array_column($batchs, 'tag_unique');
            $ids_repeat = array_repeat($ids);
            $uniques_repeat = array_repeat($uniques);
            $ids = Db::table($table)->where($pk, '>', 0)->where($pk, 'in', $ids)->where('is_delete', 0)->column($pk);
            $uniques = Db::table($table)->where($pk, 'not in', $ids)->where('tag_unique', 'in', $uniques)
                ->where('is_delete', 0)->column('tag_unique');

            $updates = $inserts = [];
            foreach ($batchs as $batch) {
                $batch['result_msg'] = [];
                if ($batch[$pk]) {
                    if (filter_var($batch[$pk], FILTER_VALIDATE_INT) === false) {
                        $batch['result_msg'][] = lang('ID只能是整数');
                    } elseif (in_array($batch[$pk], $ids_repeat)) {
                        $batch['result_msg'][] = lang('ID重复');
                    } elseif (!$import_info['is_update'] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'][] = lang('ID已存在');
                    }
                }
                if ($batch['tag_unique']) {
                    if (is_numeric($batch['tag_unique'])) {
                        $batch['result_msg'][] = lang('编号不能为纯数字');
                    } elseif (in_array($batch['tag_unique'], $uniques_repeat)) {
                        $batch['result_msg'][] = lang('编号重复');
                    } elseif (in_array($batch['tag_unique'], $uniques)) {
                        $batch['result_msg'][] = lang('编号已存在');
                    }
                }
                if ($batch['image_id']) {
                    if (!is_numeric($batch['image_id']) && filter_var($batch['image_id'], FILTER_VALIDATE_URL) === false) {
                        $batch['result_msg'][] = lang('图片必须是文件id或有效url');
                    }
                }
                if (empty($batch['tag_name'])) {
                    $batch['result_msg'][] = lang('名称不能为空');
                }
                if ($batch['create_time']) {
                    if (!strtotime($batch['create_time'])) {
                        $batch['result_msg'][] = lang('添加时间格式错误');
                    }
                }
                if ($batch['update_time']) {
                    if (!strtotime($batch['update_time'])) {
                        $batch['result_msg'][] = lang('修改时间格式错误');
                    }
                }

                if ($batch['result_msg']) {
                    $batch['result_msg'] = lang('失败：') . implode('，', $batch['result_msg']);
                    $fail[] = $batch;
                } else {
                    $batch['result_msg'] = lang('成功：');
                    $batch_tmp = $batch;
                    $batch_tmp['is_disable'] = (in_array($batch['is_disable'], ['1', lang('是')])) ? 1 : 0;
                    $batch_tmp['image_id'] = is_numeric($batch['image_id']) ? $batch['image_id'] : FileService::fileId($batch['image_id']);
                    if ($batch[$pk] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'] .= lang('修改');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? null : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? $datetime : $batch['update_time'];
                        $updates[] = $batch_tmp;
                    } else {
                        $batch['result_msg'] .= lang('添加');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? $datetime : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? null : $batch['update_time'];
                        unset($batch_tmp[$pk]);
                        $inserts[] = $batch_tmp;
                    }
                    $success[] = $batch;
                }
            }
            unset($batchs, $uniques);

            if ($updates) {
                batch_update($model, $header, $updates);
            }
            if ($inserts) {
                batch_insert($model, $header, $inserts);
            }
            if ($updates || $inserts) {
                $cache = self::cache();
                $cache->clear();
            }
            unset($updates, $inserts);
        }
        unset($import);

        return ['import_num' => $import_num, 'header' => $header, 'success' => $success, 'fail' => $fail];
    }

    /**
     * 内容标签内容列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref={Model::class}, field="tag_id")
     * @Apidoc\Query(ref={ContentService::class,"list"})
     * @Apidoc\Returned(ref={ContentService::class,"list"})
     */
    public static function contentList($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        return ContentService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 内容标签内容解除
     * @param array $tag_id      标签id
     * @param array $content_ids 内容id
     * @return int
     * @Apidoc\Param("tag_id", type="array", require=true, desc="标签id")
     * @Apidoc\Param("content_ids", type="array", require=false, desc="内容id，为空则解除所有内容")
     */
    public static function contentLift($tag_id, $content_ids = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        $content_model = new ContentModel();
        $content_pk = $content_model->getPk();

        $where[] = [$pk, 'in', $tag_id];
        if (empty($content_ids)) {
            $content_ids = AttributesModel::where($where)->column($content_pk);
        }
        $where[] = [$content_pk, 'in', $content_ids];

        $res = AttributesModel::where($where)->delete();

        $content_unique = $content_model->where($content_pk, 'in', $content_ids)->column('unique');
        $content_cache = new ContentCache();
        $content_cache->del($content_ids);
        $content_cache->del($content_unique);

        return $res;
    }

    /**
     * 获取id
     * @param string $name 名称 多个用逗号分隔
     * @return array
     */
    public static function nameId($name)
    {
        $model = self::model();
        $pk    = $model->getPk();
        $namek = $model->namek;

        $key   = 'list-' . $namek;
        $cache = self::cache();
        $list  = $cache->get($key);
        if (empty($list)) {
            $list = self::list([], 0, 0, [], $namek, false)['list'];
            $cache->set($key, $list);
        }

        $ids   = [];
        $names = explode(separator(), $name);
        foreach ($list as $val) {
            if (in_array($val[$namek], $names)) {
                $ids[] = $val[$pk];
            }
        }

        return $ids;
    }
}
