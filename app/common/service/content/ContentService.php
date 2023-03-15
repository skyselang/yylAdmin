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
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
        'content_id/d'   => 0,
        'category_ids/a' => [],
        'tag_ids/a'      => [],
        'cover_id/d'     => 0,
        'name/s'         => '',
        'unique/s'       => '',
        'title/s'        => '',
        'keywords/s'     => '',
        'description/s'  => '',
        'content/s'      => '',
        'author/s'       => '',
        'url/s'          => '',
        'sort/d'         => 250,
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
            $field = $group . ',cover_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$group => 'desc'];
        }

        $model = $model->alias('m');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'category_ids' && is_array($wv[2])) {
                $model = $model->join('content_attributes c', 'm.content_id=c.content_id')->where('c.category_id', 'in', $wv[2]);
                unset($where[$wk]);
            }
            if ($wv[0] == 'tag_ids' && is_array($wv[2])) {
                $model = $model->join('content_attributes t', 'm.content_id=t.content_id')->where('t.tag_id', 'in', $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $count = $model->where($where)->group($group)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->with(['cover', 'categorys', 'tags'])
            ->append(['cover_url', 'category_names', 'tag_names'])
            ->hidden(['cover', 'categorys', 'tags'])
            ->page($page)->limit($limit)->order($order)->group($group)->select()->toArray();

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
            $info = $info->append(['cover_url', 'category_ids', 'tag_ids', 'category_names', 'tag_names', 'images', 'videos',  'audios', 'words', 'others'])
                ->hidden(['cover', 'categorys', 'tags', 'files'])
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
            $model->categorys()->saveAll($param['category_ids']);
            // 添加标签
            $model->tags()->saveAll($param['tag_ids']);
            // 添加文件
            $image_ids = file_ids($param['images']);
            $video_ids = file_ids($param['videos']);
            $audio_ids = file_ids($param['audios']);
            $word_ids  = file_ids($param['words']);
            $other_ids = file_ids($param['others']);
            $model->files()->saveAll($image_ids, ['file_type' => 'image'], true);
            $model->files()->saveAll($video_ids, ['file_type' => 'video'], true);
            $model->files()->saveAll($audio_ids, ['file_type' => 'audio'], true);
            $model->files()->saveAll($word_ids, ['file_type' => 'word'], true);
            $model->files()->saveAll($other_ids, ['file_type' => 'other'], true);
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

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $unique = $model->where($pk, 'in', $ids)->column('unique');
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (isset($param['category_ids']) || isset($param['tag_ids']) || isset($param['images']) || isset($param['videos']) || isset($param['audios']) || isset($param['words']) || isset($param['others'])) {
                foreach ($ids as $id) {
                    $info = $model->append(['category_ids', 'tag_ids'])->find($id);
                    // 修改分类
                    if (isset($param['category_ids'])) {
                        if ($info['category_ids'] ?? []) {
                            $info->categorys()->detach($info['category_ids']);
                        }
                        $info->categorys()->saveAll($param['category_ids']);
                    }
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        if ($info['tag_ids'] ?? []) {
                            $info->tags()->detach($info['tag_ids']);
                        }
                        $info->tags()->saveAll($param['tag_ids']);
                    }
                    // 修改文件
                    if (isset($param['images']) || isset($param['videos']) || isset($param['audios']) || isset($param['words']) || isset($param['others'])) {
                        $info->files()->detach();
                        $image_ids = file_ids($param['images']);
                        $video_ids = file_ids($param['videos']);
                        $audio_ids = file_ids($param['audios']);
                        $word_ids  = file_ids($param['words']);
                        $other_ids = file_ids($param['others']);
                        $info->files()->saveAll($image_ids, ['file_type' => 'image'], true);
                        $info->files()->saveAll($video_ids, ['file_type' => 'video'], true);
                        $info->files()->saveAll($audio_ids, ['file_type' => 'audio'], true);
                        $info->files()->saveAll($word_ids, ['file_type' => 'word'], true);
                        $info->files()->saveAll($other_ids, ['file_type' => 'other'], true);
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

        return $update;
    }

    /**
     * 内容上一条
     *
     * @param int $id   内容id
     * @param int $cate 是否当前分类
     * 
     * @return array 上一条内容
     */
    public static function prev($id, $cate = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $where[] = [$pk, '<', $id];
        $where[] = where_delete();
        if ($cate) {
            $content = self::info($id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $info = $model->field($pk . ',name')->where($where)->order($pk, 'desc')->find();
        if (empty($info)) {
            return [];
        }
        $info = $info->toArray();

        return $info;
    }

    /**
     * 内容下一条
     *
     * @param int $id   内容id
     * @param int $cate 是否当前分类
     * 
     * @return array 下一条内容
     */
    public static function next($id, $cate = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $where[] = [$pk, '>', $id];
        $where[] = where_delete();
        if ($cate) {
            $content = self::info($id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $info = $model->field($pk . ',name')->where($where)->order($pk, 'asc')->find();
        if (empty($info)) {
            return [];
        }
        $info = $info->toArray();

        return $info;
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
