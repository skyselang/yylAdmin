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
use app\common\cache\content\ContentCache as Cache;
use app\common\model\content\ContentModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\ImportService;
use app\common\model\content\CategoryModel;
use app\common\model\content\TagModel;
use app\common\model\content\AttributesModel;
use app\common\service\file\FileService;
use think\facade\Db;

/**
 * 内容管理
 */
class ContentService
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
        'content_id'     => '',
        'unique/s'       => '',
        'image_id/d'     => 0,
        'name/s'         => '',
        'title/s'        => '',
        'keywords/s'     => '',
        'description/s'  => '',
        'category_ids/a' => [],
        'tag_ids/a'      => [],
        'content/s'      => '',
        'source/s'       => '',
        'author/s'       => '',
        'url/s'          => '',
        'is_top/d'       => 0,
        'is_hot/d'       => 0,
        'is_rec/d'       => 0,
        'release_time/s' => '',
        'hits_initial/d' => 0,
        'remark/s'       => '',
        'sort/d'         => 250,
        'images/a'       => [],
        'videos/a'       => [],
        'audios/a'       => [],
        'words/a'        => [],
        'others/a'       => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique', 'image_id', 'category_ids', 'tag_ids', 'is_top', 'is_hot', 'is_rec', 'release_time', 'hits_initial'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("categorys", ref={CategoryModel::class}, type="tree", desc="分类树形", field="category_id,category_pid,category_name"),
     *   @Apidoc\Returned("tags", ref={TagService::class,"info"}, type="array", desc="标签列表", field="tag_id,tag_name")
     * })
     */
    public static function basedata($exp = false)
    {
        $exps      = $exp ? where_exps() : [];
        $categorys = CategoryService::list('tree', [where_delete()], [], 'category_name');
        $tags      = TagService::list([where_delete()], 0, 0, [], 'tag_name', false)['list'] ?? [];

        return ['exps' => $exps, 'categorys' => $categorys, 'tags' => $tags];
    }

    /**
     * 内容列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="content_id,unique,image_id,name,is_top,is_hot,is_rec,is_disable,hits,release_time,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getImageUrlAttr"}, field="image_url"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsTopNameAttr"}, field="is_top_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsHotNameAttr"}, field="is_hot_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsRecNameAttr"}, field="is_rec_name"),
     *   @Apidoc\Returned(ref={Model::class,"getHitsShowAttr"}, field="hits_show"),
     *   @Apidoc\Returned(ref={Model::class,"getCategoryNamesAttr"}, field="category_names"),
     *   @Apidoc\Returned(ref={Model::class,"getTagNamesAttr"}, field="tag_names"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $group => 'desc'];
        }
        if (empty($field)) {
            $field = "$group,unique,image_id,name,is_top,is_hot,is_rec,is_disable,hits,release_time,remark,sort,create_time,update_time";
        } else {
            $field = "$group,$field";
        }

        $wt = 'content_attributes ';
        $wa = 'b';
        $model = $model->alias('a');
        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'category_ids') {
                $wa++;
                $model = $model->join($wt . $wa, "a.content_id=$wa.content_id");
                $where[$wk] = ["$wa.category_id", $wv[1], $wv[2]];
            } elseif ($wv[0] === 'category_id') {
                $wa++;
                $model = $model->join($wt . $wa, "a.content_id=$wa.content_id");
                $where_scope[] = ["$wa.category_id", $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === 'tag_ids') {
                $wa++;
                $model = $model->join($wt . $wa, "a.content_id=$wa.content_id");
                $where[$wk] = ["$wa.tag_id", $wv[1], $wv[2]];
            } elseif ($wv[0] === 'tag_id') {
                $wa++;
                $model = $model->join($wt . $wa, "a.content_id=$wa.content_id");
                $where_scope[] = ["$wa.tag_id", $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === $pk) {
                $where[$wk] = ["a.{$wv[0]}", $wv[1], $wv[2]];
            }
        }
        $where = array_values($where);

        $with     = ['categorys', 'tags'];
        $append   = ['category_names', 'category_full_names', 'tag_names'];
        $hidden   = ['categorys', 'tags'];
        $field_no = [];
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
        if (strpos($field, 'hits')) {
            $append[] = 'hits_show';
        }
        if (strpos($field, 'is_top')) {
            $append[] = 'is_top_name';
        }
        if (strpos($field, 'is_hot')) {
            $append[] = 'is_hot_name';
        }
        if (strpos($field, 'is_rec')) {
            $append[] = 'is_rec_name';
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
            $count = model_where(clone $model, $where, $where_scope)->group($group)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where, $where_scope);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->group($group)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 内容信息
     * @param int|string $id   内容id、编号
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="content_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getImageUrlAttr"}, field="image_url"),
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     * @Apidoc\Returned(ref={Model::class,"getIsTopNameAttr"}, field="is_top_name"),
     * @Apidoc\Returned(ref={Model::class,"getIsHotNameAttr"}, field="is_hot_name"),
     * @Apidoc\Returned(ref={Model::class,"getIsRecNameAttr"}, field="is_rec_name"),
     * @Apidoc\Returned(ref={Model::class,"getHitsShowAttr"}, field="hits_show"),
     * @Apidoc\Returned(ref={Model::class,"getCategoryNamesAttr"}, field="category_names"),
     * @Apidoc\Returned(ref={Model::class,"getCategoryFullNamesAttr"}, field="category_full_names"),
     * @Apidoc\Returned(ref={Model::class,"getTagNamesAttr"}, field="tag_names"),
     * @Apidoc\Returned(ref="imagesReturn")
     * @Apidoc\Returned(ref="videosReturn")
     * @Apidoc\Returned(ref="audiosReturn")
     * @Apidoc\Returned(ref="wordsReturn")
     * @Apidoc\Returned(ref="othersReturn")
     */
    public static function info($id, $exce = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('内容不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['image_url', 'category_ids', 'category_names', 'category_full_names', 'tag_ids', 'tag_names', 'images', 'videos', 'audios', 'words', 'others', 'hits_show', 'is_top_name', 'is_hot_name', 'is_rec_name', 'is_disable_name'])
                ->hidden(['image', 'categorys', 'tags', 'files'])
                ->toArray();

            $cache->set($id, $info);
        }

        // 点击量
        $model->where($pk, $id)->inc('hits', 1)->update();

        return $info;
    }

    /**
     * 内容添加
     * @param array $param 内容信息
     * @Apidoc\Param(ref={Model::class}, withoutField="content_id,hits,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getCategoryIdsAttr"}, field="category_ids")
     * @Apidoc\Param(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Param(ref="imagesParam")
     * @Apidoc\Param(ref="videosParam")
     * @Apidoc\Param(ref="audiosParam")
     * @Apidoc\Param(ref="wordsParam")
     * @Apidoc\Param(ref="othersParam")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
        }
        if (empty($param['release_time'] ?? '')) {
            $param['release_time'] = datetime();
        }
        if (empty($param['create_uid'] ?? '')) {
            $param['create_uid']  = user_id();
        }
        if (empty($param['create_time'] ?? '')) {
            $param['create_time'] = datetime();
        }

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加分类
            if (isset($param['category_ids'])) {
                $model->categorys()->saveAll($param['category_ids']);
            }
            // 添加标签
            if (isset($param['tag_ids'])) {
                $model->tags()->saveAll($param['tag_ids']);
            }
            // 添加文件
            if (isset($param['images'])) {
                $model->files()->saveAll(file_ids($param['images']), ['file_type' => 'image'], true);
            }
            if (isset($param['videos'])) {
                $model->files()->saveAll(file_ids($param['videos']), ['file_type' => 'video'], true);
            }
            if (isset($param['audios'])) {
                $model->files()->saveAll(file_ids($param['audios']), ['file_type' => 'audio'], true);
            }
            if (isset($param['words'])) {
                $model->files()->saveAll(file_ids($param['words']), ['file_type' => 'word'], true);
            }
            if (isset($param['others'])) {
                $model->files()->saveAll(file_ids($param['others']), ['file_type' => 'other'], true);
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
     * 内容修改 
     * @param int|array $ids   内容id
     * @param array     $param 内容信息
     * @Apidoc\Query(ref={Model::class})
     * @Apidoc\Param(ref={Model::class}, withoutField="hits,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getCategoryIdsAttr"}, field="category_ids")
     * @Apidoc\Param(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Param(ref="imagesParam")
     * @Apidoc\Param(ref="videosParam")
     * @Apidoc\Param(ref="audiosParam")
     * @Apidoc\Param(ref="wordsParam")
     * @Apidoc\Param(ref="othersParam")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        if (empty($param['update_uid'] ?? '')) {
            $param['update_uid']  = user_id();
        }
        if (empty($param['update_time'] ?? '')) {
            $param['update_time'] = datetime();
        }

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['category_ids', 'tag_ids', 'images', 'videos', 'audios', 'words', 'others'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改分类
                    if (isset($param['category_ids'])) {
                        $info = $info->append(['category_ids']);
                        model_relation_update($info, $info['category_ids'], $param['category_ids'], 'categorys');
                    }
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        $info = $info->append(['tag_ids']);
                        model_relation_update($info, $info['tag_ids'], $param['tag_ids'], 'tags');
                    }
                    // 修改文件
                    if (isset($param['images'])) {
                        $info = $info->append(['image_ids']);
                        model_relation_update($info, $info['image_ids'], file_ids($param['images']), 'files', ['file_type' => 'image']);
                    }
                    if (isset($param['videos'])) {
                        $info = $info->append(['video_ids']);
                        model_relation_update($info, $info['video_ids'], file_ids($param['videos']), 'files', ['file_type' => 'video']);
                    }
                    if (isset($param['audios'])) {
                        $info = $info->append(['audio_ids']);
                        model_relation_update($info, $info['audio_ids'], file_ids($param['audios']), 'files', ['file_type' => 'audio']);
                    }
                    if (isset($param['words'])) {
                        $info = $info->append(['word_ids']);
                        model_relation_update($info, $info['word_ids'], file_ids($param['words']), 'files', ['file_type' => 'word']);
                    }
                    if (isset($param['others'])) {
                        $info = $info->append(['other_ids']);
                        model_relation_update($info, $info['other_ids'], file_ids($param['others']), 'files', ['file_type' => 'other']);
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
     * 内容删除
     * @param int|array $ids  内容id
     * @param bool      $real 是否真实删除
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
                    // 删除分类
                    $info->categorys()->detach();
                    // 删除标签
                    $info->tags()->detach();
                    // 删除文件
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
     * 内容分类是否禁用
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
     * 内容批量修改
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
     * 内容导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $image_id = $exp_imp == 'export' ? 'image_url' : 'image_id';
        $category_ids = $exp_imp == 'export' ? 'category_full_names' : 'category_ids';
        $tag_ids = $exp_imp == 'export' ? 'tag_names' : 'tag_ids';
        $is_top = $exp_imp == 'export' ? 'is_top_name' : 'is_top';
        $is_hot = $exp_imp == 'export' ? 'is_hot_name' : 'is_hot';
        $is_rec = $exp_imp == 'export' ? 'is_rec_name' : 'is_rec';
        $hits = $exp_imp == 'export' ? 'hits_show' : 'hits';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'unique', 'name' => lang('编号'), 'width' => 22],
            ['field' => $image_id, 'name' => lang('图片(id或url)'), 'width' => 16],
            ['field' => 'name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => $category_ids, 'name' => lang('分类'), 'width' => 30],
            ['field' => $tag_ids, 'name' => lang('标签'), 'width' => 22],
            ['field' => $is_top, 'name' => lang('置顶'), 'width' => 10],
            ['field' => $is_hot, 'name' => lang('热门'), 'width' => 10],
            ['field' => $is_rec, 'name' => lang('推荐'), 'width' => 10],
            ['field' => $hits, 'name' => lang('点击'), 'width' => 10],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'release_time', 'name' => lang('发布时间'), 'width' => 22],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'content', 'name' => lang('内容'), 'width' => 50],
            ['field' => 'title', 'name' => lang('标题'), 'width' => 22],
            ['field' => 'keywords', 'name' => lang('关键词'), 'width' => 22],
            ['field' => 'description', 'name' => lang('描述'), 'width' => 22],
            ['field' => 'source', 'name' => lang('来源'), 'width' => 12],
            ['field' => 'author', 'name' => lang('作者'), 'width' => 12],
            ['field' => 'url', 'name' => lang('链接'), 'width' => 16],
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
     * 内容导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_CONTENT;

        $field = 'unique,image_id,name,is_top,is_hot,is_rec,is_disable,hits,release_time,remark,sort,create_time,update_time,content,title,keywords,description,source,author,url';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 内容导入
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
            $import_info['type'] = FileSettingService::EXPIMP_TYPE_CONTENT;
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
        $batch_num = 2000;

        while (count($import) > 0) {
            $batchs = array_splice($import, 0, $batch_num);
            foreach ($batchs as $key => $val) {
                $temp = [];
                foreach ($header as $vh) {
                    if ($vh['index'] > -1) {
                        $temp[$vh['field']] = $val[$vh['index']] ?? '';
                    }
                }
                $batchs[$key] = $temp;
            }

            $ids = array_column($batchs, $pk);
            $uniques = array_column($batchs, 'unique');
            $ids_repeat = array_repeat($ids);
            $uniques_repeat = array_repeat($uniques);
            $ids = Db::table($table)->where($pk, '>', 0)->where($pk, 'in', $ids)->where('is_delete', 0)->column($pk);
            $uniques = Db::table($table)->where($pk, 'not in', $ids)->where('unique', 'in', $uniques)
                ->where('is_delete', 0)->column('unique');

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
                if ($batch['unique']) {
                    if (is_numeric($batch['unique'])) {
                        $batch['result_msg'][] = lang('编号不能为纯数字');
                    } elseif (in_array($batch['unique'], $uniques_repeat)) {
                        $batch['result_msg'][] = lang('编号重复');
                    } elseif (in_array($batch['unique'], $uniques)) {
                        $batch['result_msg'][] = lang('编号已存在');
                    }
                }
                if ($batch['image_id']) {
                    if (!is_numeric($batch['image_id']) && filter_var($batch['image_id'], FILTER_VALIDATE_URL) === false) {
                        $batch['result_msg'][] = lang('图片必须是文件id或有效url');
                    }
                }
                if (empty($batch['name'])) {
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
                    $batch_tmp['category_ids'] = CategoryService::fullPathId($batch['category_ids']);
                    $batch_tmp['tag_ids'] = TagService::nameId($batch['tag_ids']);
                    $batch_tmp['is_top'] = (in_array($batch['is_top'], ['1', lang('是')])) ? 1 : 0;
                    $batch_tmp['is_hot'] = (in_array($batch['is_hot'], ['1', lang('是')])) ? 1 : 0;
                    $batch_tmp['is_rec'] = (in_array($batch['is_rec'], ['1', lang('是')])) ? 1 : 0;
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

            $attr_adds = [];
            if ($updates) {
                foreach ($updates as $key => $update) {
                    if ($update['category_ids']) {
                        foreach ($update['category_ids'] as $category_id) {
                            $attr_adds[] = [$pk => $update[$pk], 'category_id' => $category_id, 'tag_id' => 0];
                        }
                    }
                    if ($update['tag_ids']) {
                        foreach ($update['tag_ids'] as $tag_id) {
                            $attr_adds[] = [$pk => $update[$pk], 'category_id' => 0, 'tag_id' => $tag_id];
                        }
                    }
                    unset($update['category_ids'], $update['tag_ids']);
                    $updates[$key] = $update;
                }
            }
            if ($inserts) {
                foreach ($inserts as $key => $insert) {
                    $insert_tmp = $insert;
                    unset($insert_tmp['category_ids'], $insert_tmp['tag_ids']);
                    $id = Db::table($table)->insertGetId($insert_tmp);
                    if ($insert['category_ids']) {
                        foreach ($insert['category_ids'] as $category_id) {
                            $attr_adds[] = [$pk => $id, 'category_id' => $category_id, 'tag_id' => 0];
                        }
                    }
                    if ($insert['tag_ids']) {
                        foreach ($insert['tag_ids'] as $tag_id) {
                            $attr_adds[] = [$pk => $id, 'category_id' => 0, 'tag_id' => $tag_id];
                        }
                    }
                }
            }
            $batch_header = $header;
            foreach ($batch_header as $key => $val) {
                if (in_array($val['field'], ['category_ids', 'tag_ids'])) {
                    unset($batch_header[$key]);
                }
            }
            $attr_del_ids = array_column($updates, $pk);
            batch_update($model, $batch_header, $updates);
            self::deleCategoryAttr($attr_del_ids);
            self::deleTagAttr($attr_del_ids);
            if ($attr_adds) {
                AttributesModel::insertAll($attr_adds);
            }
            if ($updates || $inserts) {
                $cache = self::cache();
                $cache->clear();
            }
            unset($updates, $inserts, $attr_adds, $attr_del_ids);
        }
        unset($import);

        return ['import_num' => $import_num, 'header' => $header, 'success' => $success, 'fail' => $fail];
    }

    /**
     * 内容统计
     * @return array
     * @Apidoc\Returned("category", type="int", desc="分类总数")
     * @Apidoc\Returned("content", type="int", desc="内容总数")
     * @Apidoc\Returned("x_data", type="array", desc="图表xAxis.data")
     * @Apidoc\Returned("s_data", type="array", desc="图表series.data")
     */
    public static function statistic()
    {
        $cache = self::cache();
        $key = 'statistic';
        $data = $cache->get($key);
        if (empty($data)) {
            $CategoryModel = new CategoryModel();
            $category_count = $CategoryModel->where([where_delete()])->count();

            $ContentModel = self::model();
            $content_count = $ContentModel->where([where_delete()])->count();

            $AttributesModel = new AttributesModel();
            $categorys = $AttributesModel->alias('a')
                ->join('content c', 'a.content_id=c.content_id', 'left')
                ->join('content_category cc', 'a.category_id=cc.category_id', 'left')
                ->field('a.category_id,count(a.category_id) as content_count,cc.category_name')
                ->where('a.category_id', '>', 0)
                ->where('c.is_delete', '=', 0)
                ->order('content_count', 'asc')
                ->group('a.category_id')
                ->select();

            $x_data = $s_data = [];
            foreach ($categorys as $v) {
                $x_data[] = $v['category_name'];
                $s_data[] = $v['content_count'];
            }

            $data['category'] = $category_count;
            $data['content']  = $content_count;
            $data['x_data']   = $x_data;
            $data['s_data']   = $s_data;

            $cache->set($key, $data);
        }

        return $data;
    }

    /**
     * 内容上/下一条
     * @param int    $id    内容id
     * @param string $type  prev上一条，next下一条
     * @param array  $where 内容条件
     * @return array 内容
     */
    public static function prevNext($id, $type = 'prev', $where = [])
    {
        if ($type == 'next') {
            $where[] = ['a.content_id', '>', $id];
            $order = ['a.content_id' => 'asc'];
        } else {
            $where[] = ['a.content_id', '<', $id];
            $order = ['a.content_id' => 'desc'];
        }
        $where[] = ['release_time', '<=', datetime()];
        $where[] = where_disable();
        $where[] = where_delete();

        $field = 'unique,image_id,title';

        $info = self::list($where, 0, 1, $order, $field)['list'];

        return $info[0] ?? [];
    }

    /**
     * 删除关联分类
     * @param array $ids id
     * @return int
     */
    public static function deleCategoryAttr($ids)
    {
        if (empty($ids)) {
            return 0;
        }
        $model          = self::model();
        $pk             = $model->getPk();
        $category_model = new CategoryModel();
        $category_pk    = $category_model->getPk();
        $res            = AttributesModel::where($pk, 'in', $ids)->where($category_pk, '>', 0)->delete();
        return $res;
    }

    /**
     * 删除关联标签
     * @param array $ids id
     * @return int
     */
    public static function deleTagAttr($ids)
    {
        if (empty($ids)) {
            return 0;
        }
        $model     = self::model();
        $pk        = $model->getPk();
        $tag_model = new TagModel();
        $tag_pk    = $tag_model->getPk();
        $res       = AttributesModel::where($pk, 'in', $ids)->where($tag_pk, '>', 0)->delete();
        return $res;
    }
}
