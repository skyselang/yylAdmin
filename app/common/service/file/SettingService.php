<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\file\SettingCache as Cache;
use app\common\model\file\SettingModel as Model;

/**
 * 文件设置
 */
class SettingService
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
     * 基础数据
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned("storages", type="object", desc="存储方式"),
     *   @Apidoc\Returned("file_types", type="object", desc="文件类型"),
     *   @Apidoc\Returned("groups", type="array", ref={GroupService::class,"info"}, field="group_id,group_name", desc="分组列表"),
     *   @Apidoc\Returned("tags", type="array", ref={TagService::class,"info"}, field="tag_id,tag_name", desc="标签列表"),
     * })
     */
    public static function basedata()
    {
        $storages   = self::storages('', true);
        $file_types = self::fileTypes();
        $groups     = GroupService::list([where_delete()], 0, 0, [], 'group_name', false)['list'] ?? [];
        $tags       = TagService::list([where_delete()], 0, 0, [], 'tag_name', false)['list'] ?? [];

        return ['storages' => $storages, 'file_types' => $file_types, 'groups' => $groups, 'tags' => $tags];
    }

    /**
     * 文件设置id
     */
    private static $id = 1;

    /**
     * 文件设置信息
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @return array
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned("accept_ext", type="string", desc="允许上传的文件后缀")
     * @Apidoc\Returned("image_exts", type="array", desc="支持图片格式")
     * @Apidoc\Returned("video_exts", type="array", desc="支持视频格式")
     * @Apidoc\Returned("audio_exts", type="array", desc="支持音频格式")
     * @Apidoc\Returned("word_exts", type="array", desc="支持文档格式")
     * @Apidoc\Returned("other_exts", type="string", desc="除图片、视频、音频、文档的其他格式")
     */
    public static function info($fields = '')
    {
        $cache = self::cache();
        $id    = self::$id;
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();
            $info['accept_ext'] = self::fileAccept($info);
            $info['file_types'] = self::fileTypes();
            $exts = ['image_ext', 'video_ext', 'audio_ext', 'word_ext'];
            foreach ($exts as $ext) {
                $info[$ext] = explode(',', $info[$ext]);
            }
            $info = array_merge($info, self::fileType('', true));

            $cache->set($id, $info);
        }

        if ($fields) {
            $data   = [];
            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                $field = trim($field);
                if (isset($info[$field])) {
                    $data[$field] = $info[$field];
                }
            }
            return $data;
        }

        return $info;
    }

    /**
     * 文件设置修改
     * @param array $param 设置信息
     * @Apidoc\Param(ref={Model::class},withoutField="setting_id,create_uid,update_uid,create_time,update_time")
     */
    public static function edit($param)
    {
        $model = self::model();
        $id    = self::$id;

        $exts = ['image_ext', 'video_ext', 'audio_ext', 'word_ext'];
        foreach ($exts as $ext) {
            if (isset($param[$ext])) {
                $param[$ext] = implode(',', $param[$ext]);
            }
        }
        if (isset($param['other_ext'])) {
            $param['other_ext'] = trim($param['other_ext']);
            $param['other_ext'] = trim($param['other_ext'], ',');
            $param['other_ext'] = str_replace(' ', ',', $param['other_ext']);
            $param['other_ext'] = str_replace('，', ',', $param['other_ext']);
        }

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $info = $model->find($id);
        $res  = $info->save($param);
        if (empty($res)) {
            exception();
        }

        $cache = self::cache();
        $cache->del($id);

        return $param;
    }

    /**
     * 文件类型数组或名称
     * @param string $file_type
     * @param bool   $val_lab 是否返回带value、label下标的数组
     */
    public static function fileTypes($file_type = '', $val_lab = false)
    {
        $file_types = [
            'image' => lang('图片'),
            'video' => lang('视频'),
            'audio' => lang('音频'),
            'word'  => lang('文档'),
            'other' => lang('其它'),
        ];
        if ($file_type !== '') {
            return $file_types[$file_type] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($file_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $file_types;
    }

    /**
     * 文件新增方式数组或名称
     * @param string $add_type
     * @param bool   $val_lab 是否返回带value、label下标的数组
     */
    public static function addTypes($add_type = '', $val_lab = false)
    {
        $add_types = [
            'upload' => lang('上传'),
            'add'    => lang('添加'),
        ];
        if ($add_type !== '') {
            return $add_types[$add_type] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($add_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $add_types;
    }

    /**
     * 文件储存方式数组或名称
     * @param string $storage
     * @param bool   $val_lab 是否返回带value、label下标的数组
     */
    public static function storages($storage = '', $val_lab = false)
    {
        $storages = [
            'local'   => lang('本地(服务器)'),
            'qiniu'   => lang('七牛云 Kodo'),
            'aliyun'  => lang('阿里云 OSS'),
            'tencent' => lang('腾讯云 COS'),
            'baidu'   => lang('百度云 BOS'),
            'upyun'   => lang('又拍云 USS'),
            'huawei'  => lang('华为云 OBS'),
            'aws'     => lang('AWS S3'),
        ];
        if ($storage !== '') {
            return $storages[$storage] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($storages as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $storages;
    }

    /**
     * 文件大小格式化
     * @param int  $file_size 文件大小字节数
     * @param int  $precision 保留小数位数
     * @param bool $is_space  是否使用空格分隔符
     */
    public static function fileSize($file_size, $precision = 2, $is_space = false)
    {
        $units      = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB', 'BB');
        $file_size  = max($file_size, 0);
        $pow        = floor(log($file_size) / log(1024));
        $pow        = max($pow, 0);
        $file_size /= pow(1024, $pow);

        $separator = '';
        if ($is_space) {
            $separator = ' ';
        }

        return round($file_size, $precision) . $separator . $units[$pow];
    }

    /**
     * 文件类型获取
     * @param string $file_ext 文件后缀
     * @param bool   $get_exts 获取支持后缀
     */
    public static function fileType($file_ext = '', $get_exts = false)
    {
        $image_ext = [
            'jpg',
            'png',
            'jpeg',
            'ico',
            'gif',
            'bmp',
            'webp',
            'svg',
            'tif',
            'pcx',
            'tga',
            'exif',
            'psd',
            'cdr',
            'pcd',
            'dxf',
            'ufo',
            'eps',
            'ai',
            'raw',
            'wmf',
            'avif',
            'apng',
            'xbm',
            'fpx'
        ];
        $video_ext = [
            'mp4',
            'avi',
            'mkv',
            'flv',
            'rm',
            'rmvb',
            'webm',
            '3gp',
            'mpeg',
            'mpg',
            'dat',
            'asx',
            'wmv',
            'mov',
            'ogm',
            'vob'
        ];
        $audio_ext = [
            'mp3',
            'aac',
            'm4a',
            'wav',
            'wma',
            'ape',
            'flac',
            'ogg',
            'adt',
            'adts',
            'cda',
            'cd',
            'wave',
            'aiff',
            'midi',
            'ra',
            'rmx',
            'vqf',
            'amr'
        ];
        $word_ext = [
            'docx',
            'xlsx',
            'pptx',
            'doc',
            'xls',
            'ppt',
            'pdf',
            'docm',
            'dotx',
            'dotm',
            'txt',
            'xlsm',
            'xltx',
            'xltm',
            'xlsb',
            'xlam',
            'csv',
            'potx',
            'potm',
            'ppam',
            'ppsx',
            'ppsm',
            'sldx',
            'sldm',
            'thmx'
        ];

        if ($get_exts) {
            return [
                'image_exts' => $image_ext,
                'video_exts' => $video_ext,
                'audio_exts' => $audio_ext,
                'word_exts'  => $word_ext,
                'other_exts' => lang('支持除图片、视频、音频、文档的其它格式'),
            ];
        }

        if (in_array($file_ext, $image_ext)) {
            return 'image';
        } elseif (in_array($file_ext, $video_ext)) {
            return 'video';
        } elseif (in_array($file_ext, $audio_ext)) {
            return 'audio';
        } elseif (in_array($file_ext, $word_ext)) {
            return 'word';
        } else {
            return 'other';
        }
    }

    /**
     * 文件链接
     * @param array $file 文件信息
     */
    public static function fileUrl($file)
    {
        $file_url = '';
        if ($file) {
            $domain    = $file['domain'];
            $file_path = $file['file_path'];
            if ($file['storage'] === 'local' && empty($domain)) {
                $file_url = file_url($file_path);
            } else {
                $domain    = rtrim($domain, '/');
                $file_path = ltrim($file_path, '/');
                $file_url  = $domain . '/' . $file_path;
                if (strpos($file_url, 'http') !== 0) {
                    $setting  = self::info();
                    $protocol = 'http://';
                    if ($setting['is_storage_https'] ?? 0) {
                        $protocol = 'https://';
                    }
                    $file_url = $protocol . $file_url;
                }
            }
        }

        return $file_url;
    }

    /**
     * 文件上传accept
     * @param array $setting 设置信息
     */
    public static function fileAccept($setting = [])
    {
        $accept = '';
        $exts = ['image_ext', 'video_ext', 'audio_ext', 'word_ext', 'other_ext'];
        foreach ($exts as $ext) {
            if ($setting[$ext] ?? '') {
                $file_ext = explode(',', $setting[$ext]);
                foreach ($file_ext as $ve) {
                    $accept .= '.' . $ve . ',';
                }
            }
        }

        return rtrim($accept, ',');
    }

    /**
     * 文件格式数组
     * @return array 文件格式数组
     */
    public static function fileExts()
    {
        $file_exts = [];
        $type_exts = ['image_exts', 'video_exts', 'audio_exts', 'word_exts', 'other_ext'];
        $setting   = self::info();
        foreach ($type_exts as $type_ext) {
            if ($setting[$type_ext] ?? []) {
                $exts = $setting[$type_ext];
                if (is_string($exts)) {
                    $exts = explode(',', $exts);
                }
                foreach ($exts as $ext) {
                    $file_exts[] = $ext;
                }
            }
        }

        return $file_exts;
    }

    /**
     * 导出文件目录名
     */
    public const EXPORT_DIR = 'export';
    /**
     * 导入文件目录名
     */
    public const IMPORT_DIR = 'import';

    /**
     * 导出导入类型：会员
     */
    public const EXPIMP_TYPE_MEMBER = 'member';
    /**
     * 导出导入类型：会员标签
     */
    public const EXPIMP_TYPE_MEMBER_TAG = 'member_tag';
    /**
     * 导出导入类型：会员分组
     */
    public const EXPIMP_TYPE_MEMBER_GROUP = 'member_group';
    /**
     * 导出导入类型：会员接口
     */
    public const EXPIMP_TYPE_MEMBER_API = 'member_api';
    /**
     * 导出导入类型：会员日志
     */
    public const EXPIMP_TYPE_MEMBER_LOG = 'member_log';
    /**
     * 导出导入类型：会员第三方账号
     */
    public const EXPIMP_TYPE_MEMBER_THIRD = 'member_third';
    /**
     * 导出导入类型：内容
     */
    public const EXPIMP_TYPE_CONTENT = 'content';
    /**
     * 导出导入类型：内容分类
     */
    public const EXPIMP_TYPE_CONTENT_CATEGORY = 'content_category';
    /**
     * 导出导入类型：内容标签
     */
    public const EXPIMP_TYPE_CONTENT_TAG = 'content_tag';
    /**
     * 导出导入类型：文件
     */
    public const EXPIMP_TYPE_FILE = 'file';
    /**
     * 导出导入类型：文件分组
     */
    public const EXPIMP_TYPE_FILE_GROUP = 'file_group';
    /**
     * 导出导入类型：文件标签
     */
    public const EXPIMP_TYPE_FILE_TAG = 'file_tag';
    /**
     * 导出导入类型：导出文件
     */
    public const EXPIMP_TYPE_FILE_EXPORT = 'file_export';
    /**
     * 导出导入类型：导入文件
     */
    public const EXPIMP_TYPE_FILE_IMPORT = 'file_import';
    /**
     * 导出导入类型：协议
     */
    public const EXPIMP_TYPE_SETTING_ACCORD = 'setting_accord';
    /**
     * 导出导入类型：轮播
     */
    public const EXPIMP_TYPE_SETTING_CAROUSEL = 'setting_carousel';
    /**
     * 导出导入类型：反馈
     */
    public const EXPIMP_TYPE_SETTING_FEEDBACK = 'setting_feedback';
    /**
     * 导出导入类型：友链
     */
    public const EXPIMP_TYPE_SETTING_LINK = 'setting_link';
    /**
     * 导出导入类型：通告
     */
    public const EXPIMP_TYPE_SETTING_NOTICE = 'setting_notice';
    /**
     * 导出导入类型：地区
     */
    public const EXPIMP_TYPE_SETTING_REGION = 'setting_region';
    /**
     * 导出导入类型：用户
     */
    public const EXPIMP_TYPE_SYSTEM_USER = 'system_user';
    /**
     * 导出导入类型：用户日志
     */
    public const EXPIMP_TYPE_SYSTEM_USER_LOG = 'system_user_log';
    /**
     * 导出导入类型：部门
     */
    public const EXPIMP_TYPE_SYSTEM_DEPT = 'system_dept';
    /**
     * 导出导入类型：菜单
     */
    public const EXPIMP_TYPE_SYSTEM_MENU = 'system_menu';
    /**
     * 导出导入类型：公告
     */
    public const EXPIMP_TYPE_SYSTEM_NOTICE = 'system_notice';
    /**
     * 导出导入类型：职位
     */
    public const EXPIMP_TYPE_SYSTEM_POST = 'system_post';
    /**
     * 导出导入类型：角色
     */
    public const EXPIMP_TYPE_SYSTEM_ROLE = 'system_role';
    /**
     * 导出导入类型：邮件日志
     */
    public const EXPIMP_TYPE_SETTING_EMAIL_LOG = 'setting_email_log';
    /**
     * 导出导入类型：短信日志
     */
    public const EXPIMP_TYPE_SETTING_SMS_LOG = 'setting_sms_log';
    /**
     * 导出导入类型数组或名称
     * @param string $type    类型
     * @param string $exp_imp export导出，import导入
     * @param bool   $val_lab 是否返回带value、label下标的数组
     */
    public static function expImpType($type = '', $exp_imp = 'export', $val_lab = false)
    {
        $types = [
            self::EXPIMP_TYPE_MEMBER            => lang('会员'),
            self::EXPIMP_TYPE_MEMBER_TAG        => lang('会员标签'),
            self::EXPIMP_TYPE_MEMBER_GROUP      => lang('会员分组'),
            self::EXPIMP_TYPE_MEMBER_API        => lang('会员接口'),
            self::EXPIMP_TYPE_MEMBER_LOG        => lang('会员日志'),
            self::EXPIMP_TYPE_MEMBER_THIRD      => lang('会员第三方账号'),
            self::EXPIMP_TYPE_CONTENT           => lang('内容'),
            self::EXPIMP_TYPE_CONTENT_CATEGORY  => lang('内容分类'),
            self::EXPIMP_TYPE_CONTENT_TAG       => lang('内容标签'),
            self::EXPIMP_TYPE_FILE              => lang('文件'),
            self::EXPIMP_TYPE_FILE_GROUP        => lang('文件分组'),
            self::EXPIMP_TYPE_FILE_TAG          => lang('文件标签'),
            self::EXPIMP_TYPE_FILE_EXPORT       => lang('导出文件'),
            self::EXPIMP_TYPE_FILE_IMPORT       => lang('导入文件'),
            self::EXPIMP_TYPE_SETTING_ACCORD    => lang('协议'),
            self::EXPIMP_TYPE_SETTING_CAROUSEL  => lang('轮播'),
            self::EXPIMP_TYPE_SETTING_FEEDBACK  => lang('反馈'),
            self::EXPIMP_TYPE_SETTING_LINK      => lang('友链'),
            self::EXPIMP_TYPE_SETTING_NOTICE    => lang('通告'),
            self::EXPIMP_TYPE_SETTING_REGION    => lang('地区'),
            self::EXPIMP_TYPE_SYSTEM_USER       => lang('用户'),
            self::EXPIMP_TYPE_SYSTEM_USER_LOG   => lang('用户日志'),
            self::EXPIMP_TYPE_SYSTEM_DEPT       => lang('部门'),
            self::EXPIMP_TYPE_SYSTEM_MENU       => lang('菜单'),
            self::EXPIMP_TYPE_SYSTEM_NOTICE     => lang('公告'),
            self::EXPIMP_TYPE_SYSTEM_POST       => lang('职位'),
            self::EXPIMP_TYPE_SYSTEM_ROLE       => lang('角色'),
            self::EXPIMP_TYPE_SETTING_EMAIL_LOG => lang('邮件日志'),
            self::EXPIMP_TYPE_SETTING_SMS_LOG   => lang('短信日志'),
        ];
        if ($exp_imp == 'import') {
            $exp_imp_name = lang('导入');
        } else {
            $exp_imp_name = lang('导出');
        }
        foreach ($types as &$val) {
            $val .= $exp_imp_name;
        }

        if ($type !== '') {
            $type_name = $types[$type] ?? '';
            if ($type_name) {
                return $type_name;
            } else {
                if ($exp_imp == 'import') {
                    exception(lang('未知导入类型：') . $type);
                } else {
                    exception(lang('未知导出类型：') . $type);
                }
            }
        }

        if ($val_lab) {
            $val_labs = [];
            foreach ($types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }

        return $types;
    }

    /**
     * 导出导入状态：待处理
     */
    public const EXPIMP_STATUS_PENDING = 0;
    /**
     * 导出导入状态：处理中
     */
    public const EXPIMP_STATUS_PROCESSING = 1;
    /**
     * 导出导入状态：处理成功
     */
    public const EXPIMP_STATUS_SUCCESS = 2;
    /**
     * 导出导入状态：处理失败
     */
    public const EXPIMP_STATUS_FAIL = 3;
    /**
     * 导出导入状态数组或名称
     * @param string $status
     * @param bool   $val_lab 是否返回带value、label下标的数组
     */
    public static function expImpStatus($status = '', $val_lab = false)
    {
        $statuss = [
            self::EXPIMP_STATUS_PENDING    => lang('待处理'),
            self::EXPIMP_STATUS_PROCESSING => lang('处理中'),
            self::EXPIMP_STATUS_SUCCESS    => lang('处理成功'),
            self::EXPIMP_STATUS_FAIL       => lang('处理失败'),
        ];

        if ($status !== '') {
            return $statuss[$status] ?? '';
        }

        if ($val_lab) {
            $val_labs = [];
            foreach ($statuss as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }

        return $statuss;
    }

    /**
     * 导出导入文件路径
     * @param string $type    类型
     * @param string $exp_imp export导出，import导入
     */
    public static function expImpFilePath($type, $exp_imp = 'export')
    {
        if ($exp_imp == 'import') {
            $exp_imp_path = self::IMPORT_DIR;
        } else {
            $exp_imp_path = self::EXPORT_DIR;
        }
        $public_path = public_path();
        $expimp_path = 'storage/' . $exp_imp_path . '/' . date('Ym');
        $file_dir = $public_path . '/' . $expimp_path;
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0777, true);
        }
        $file_path = $expimp_path . '/' . date('Ymd-His') . '-' . $type . '-' . bin2hex(random_bytes(8)) . '.xlsx';

        return $file_path;
    }

    /**
     * 导出导入文件名称
     * @param string $type      类型
     * @param string $exp_imp   export导出，import导入
     * @param int    $is_import 是否导入模板 1是，0否
     */
    public static function expImpFileName($type, $exp_imp = 'export', $is_import = 0)
    {
        $type_name = self::expImpType($type, $exp_imp);
        if ($exp_imp == 'export' && $is_import) {
            $type_name = self::expImpType($type, 'import') . lang('模板');
        }
        $file_name = $type_name . '-' . date('Ymd-His') . '.xlsx';

        return $file_name;
    }

    /**
     * 导入文件保存路径
     * @param string $file_path 文件路径
     */
    public static function impFilePathSave($file_path)
    {
        $file_paths = explode('/', $file_path);
        $file_path = $file_paths[count($file_paths) - 2] . '/' . $file_paths[count($file_paths) - 1];

        return $file_path;
    }

    /**
     * 导入成功文件路径
     * @param string $file_path 文件路径
     */
    public static function impFilePathSuccess($file_path)
    {
        return substr($file_path, 0, -5) . '-success.xlsx';
    }

    /**
     * 导入失败文件路径
     * @param string $file_path 文件路径
     */
    public static function impFilePathFail($file_path)
    {
        return substr($file_path, 0, -5) . '-fail.xlsx';
    }
}
