<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\content;

use app\common\cache\content\ContentCache;
use app\common\model\content\ContentModel;
use app\common\model\content\CategoryModel;
use app\common\model\content\AttributesModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 内容管理
 */
class ContentService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'content_id/d'   => '',
        'unique/s'       => '',
        'category_ids/a' => [],
        'tag_ids/a'      => [],
        'image_id/d'     => 0,
        'name/s'         => '',
        'release_time/s' => '',
        'title/s'        => '',
        'keywords/s'     => '',
        'description/s'  => '',
        'content/s'      => '',
        'source/s'       => '',
        'author/s'       => '',
        'url/s'          => '',
        'remark/s'       => '',
        'sort/d'         => 250,
        'hits_initial/d' => 0,
        'images/a'       => [],
        'videos/a'       => [],
        'audios/a'       => [],
        'words/a'        => [],
        'others/a'       => [],
    ];

    /**
     * 内容列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $model = new ContentModel();
        $pk = $model->getPk();
        $group = 'm.' . $pk;

        if (empty($field)) {
            $field = $group . ',unique,image_id,name,release_time,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $group => 'desc'];
        }

        $model = $model->alias('m');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'category_ids') {
                $model = $model->join('content_attributes c', 'm.content_id=c.content_id')->where('c.category_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
            if ($wv[0] == 'tag_ids') {
                $model = $model->join('content_attributes t', 'm.content_id=t.content_id')->where('t.tag_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $with     = ['categorys', 'tags'];
        $append   = ['category_names', 'tag_names'];
        $hidden   = ['categorys', 'tags'];
        $field_no = [];
        if (strpos($field, 'image_id') !== false) {
            $with[]   = $hidden[]   = 'image';
            $append[] = 'image_url';
        }
        if (strpos($field, 'images') !== false) {
            $with[]   = $hidden[]   = 'files';
            $append[] = $field_no[] = 'images';
        } elseif (strpos($field, 'image_urls') !== false) {
            $with[]   = $hidden[]   = 'files';
            $append[] = $field_no[] = 'image_urls';
        }
        if (strpos($field, 'hits') !== false) {
            $append[] = 'hits_show';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $model->where($where)->group($group)->count();
        $pages = 0;
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->group($group)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 内容信息
     * 
     * @param int|string $id   内容id、标识
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $info = ContentCache::get($id);
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
                    exception('内容不存在：' . $id);
                }
                return [];
            }
            $info = $info
                ->append(['image_url', 'category_ids', 'tag_ids', 'category_names', 'tag_names', 'images', 'videos', 'audios', 'words', 'others', 'hits_show'])
                ->hidden(['image', 'categorys', 'tags', 'files'])
                ->toArray();

            ContentCache::set($id, $info);
        }

        // 点击量
        $model->where($pk, $id)->inc('hits', 1)->update();

        return $info;
    }

    /**
     * 内容添加
     *
     * @param array $param 内容信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

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
     *     
     * @param int|array $ids   内容id
     * @param array     $param 内容信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new ContentModel();
        $pk = $model->getPk();

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
            if (var_isset($param, ['category_ids', 'tag_ids', 'images', 'videos', 'audios', 'words', 'others'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改分类
                    if (isset($param['category_ids'])) {
                        $info = $info->append(['category_ids']);
                        relation_update($info, $info['category_ids'], $param['category_ids'], 'categorys');
                    }
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        $info = $info->append(['tag_ids']);
                        relation_update($info, $info['tag_ids'], $param['tag_ids'], 'tags');
                    }
                    // 修改文件
                    if (isset($param['images'])) {
                        $info = $info->append(['image_ids']);
                        relation_update($info, $info['image_ids'], file_ids($param['images']), 'files', ['file_type' => 'image']);
                    }
                    if (isset($param['videos'])) {
                        $info = $info->append(['video_ids']);
                        relation_update($info, $info['video_ids'], file_ids($param['videos']), 'files', ['file_type' => 'video']);
                    }
                    if (isset($param['audios'])) {
                        $info = $info->append(['audio_ids']);
                        relation_update($info, $info['audio_ids'], file_ids($param['audios']), 'files', ['file_type' => 'audio']);
                    }
                    if (isset($param['words'])) {
                        $info = $info->append(['word_ids']);
                        relation_update($info, $info['word_ids'], file_ids($param['words']), 'files', ['file_type' => 'word']);
                    }
                    if (isset($param['others'])) {
                        $info = $info->append(['other_ids']);
                        relation_update($info, $info['other_ids'], file_ids($param['others']), 'files', ['file_type' => 'other']);
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

        ContentCache::del($ids);
        ContentCache::del($unique);

        return $param;
    }

    /**
     * 内容删除
     * 
     * @param int|array $ids  内容id
     * @param bool      $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

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
                $update = delete_update();
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

        ContentCache::del($ids);
        ContentCache::del($unique);

        return $update;
    }

    /**
     * 内容上/下一条
     *
     * @param int    $id    内容id
     * @param string $type  prev上一条，next下一条
     * @param array  $where 内容条件
     * 
     * @return array 内容
     */
    public static function prevNext($id, $type = 'prev', $where = [])
    {
        if ($type == 'next') {
            $where[] = ['m.content_id', '>', $id];
            $order = ['m.content_id' => 'asc'];
        } else {
            $where[] = ['m.content_id', '<', $id];
            $order = ['m.content_id' => 'desc'];
        }

        $where[] = ['release_time', '<=', datetime()];
        $where[] = where_disable();
        $where[] = where_delete();

        $field = 'm.content_id,unique,image_id,name';

        $info = self::list($where, 0, 1, $order, $field)['list'];
        if (empty($info[0] ?? [])) {
            return [];
        }

        return $info[0];
    }

    /**
     * 内容统计
     * @Apidoc\Returned("category", type="int", desc="分类总数")
     * @Apidoc\Returned("content", type="int", desc="内容总数")
     * @Apidoc\Returned("x_data", type="array", desc="图表xAxis.data")
     * @Apidoc\Returned("s_data", type="array", desc="图表series.data")
     * @return array
     */
    public static function statistic()
    {
        $key = 'statistic';
        $data = ContentCache::get($key);
        if (empty($data)) {
            $CategoryModel = new CategoryModel();
            $category_count = $CategoryModel->where([where_delete()])->count();

            $ContentModel = new ContentModel();
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

            ContentCache::set($key, $data);
        }

        return $data;
    }
}
