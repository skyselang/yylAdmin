<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Overtrue\Pinyin\Pinyin;

/**
 * 语言工具
 * 查找缺失语言变量并写入语言文件
 */
class Lang extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('lang')
            ->setDescription('语言工具，查找缺失语言变量并写入语言文件');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('lang:execute');
        $this->lang();
    }

    /**
     * 扫描并处理多语言文件
     * 
     * 该方法会扫描指定目录下的所有语言变量调用，并分别处理中文和英文语言文件，
     * 将缺失的语言变量写入对应的语言文件中
     */
    protected function lang()
    {
        $output = new Output();
        $output->writeln('查找缺失语言变量并写入语言文件');

        // 扫描所有语言变量
        $output->writeln('扫描目录下的所有语言变量...');
        $scanPath = $this->langDirSeparator(root_path() . 'app');
        $output->writeln('扫描目录路径：' . $scanPath);
        $usedLang = $this->langCall($scanPath);

        // 处理中文语言文件
        $zhLangFile = $this->langDirSeparator(root_path() . 'app/lang/zh-cn.php');
        $this->langFile($zhLangFile, $usedLang);

        // 处理英文语言文件
        $enLangFile = $this->langDirSeparator(root_path() . 'app/lang/en.php');
        $this->langFile($enLangFile, $usedLang);
    }

    /**
     * 扫描指定目录下的PHP文件，提取所有lang()函数调用的翻译键名
     * 
     * @param string $dir 要扫描的目录路径
     * @return array 返回所有找到的翻译键名数组
     * 
     * 功能说明：
     * - 递归遍历目录下的所有PHP文件
     * - 使用正则匹配查找lang('key')或lang("key")形式的调用
     * - 返回去重后的翻译键名列表
     */
    protected function langCall($dir)
    {
        $langVars = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $ignoreFile = [__FILE__];
        foreach ($ignoreFile as $key => $val) {
            $ignoreFile[$key] = $this->langDirSeparator($val);
        }

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                $filePath = $this->langDirSeparator($filePath);
                if (!in_array($filePath, $ignoreFile)) {
                    $content = file_get_contents($file->getPathname());
                    if (preg_match_all('/lang\([\'\"](.*?)[\'\"]/', $content, $matches)) {
                        foreach ($matches[1] as $match) {
                            $langVars[$match] = true;
                        }
                    }
                }
            }
        }

        return array_keys($langVars);
    }

    /**
     * 处理语言文件并添加缺失的语言变量
     * 
     * @param string $langFile 语言文件路径
     * @param array $usedLang 已使用的语言变量数组
     * @return void
     * 
     * 功能说明：
     * - 检查语言文件是否存在
     * - 读取现有语言文件内容
     * - 对比找出缺失的语言变量
     * - 按拼音排序缺失变量
     * - 在文件末尾插入缺失变量
     * - 保存更新后的语言文件
     * 
     * 注意事项：
     * - 语言文件必须是有效的PHP数组文件
     * - 会直接修改原始语言文件
     */
    protected function langFile($langFile, $usedLang)
    {
        $output = new Output();
        $output->writeln('处理语言文件：' . $langFile);

        if (!file_exists($langFile)) {
            $output->writeln('语言文件不存在：' . $langFile);
            return;
        }

        // 读取现有语言文件
        $langContent = file_get_contents($langFile);

        // 提取现有语言变量
        $existingLang = include($langFile);

        // 找出缺失的变量
        $missingLang = [];
        $missingLangPy = [];
        foreach ($usedLang as $key) {
            if (!isset($existingLang[$key])) {
                $missingLang[] = $key;
                $pinyin = Pinyin::sentence($key, 'none');
                $missingLangPy[] = (string) $pinyin;
            }
        }

        $log['lang_file'] = $langFile;
        $log['lang_miss'] = $missingLang;
        $this->log($log);
        
        // 如果有缺失的变量
        if (!empty($missingLang)) {
            array_multisort($missingLangPy, $missingLang);

            $output->writeln('发现以下缺失的语言变量：');
            foreach ($missingLang as $key) {
                $output->writeln('- ' . $key);
            }

            // 找到文件中最后一个语言变量的位置
            if (preg_match('/\'(.*?)\'\s*=>\s*\'(.*?)\'(?=,\s*\];)/', $langContent, $lastMatch, PREG_OFFSET_CAPTURE)) {
                $position = $lastMatch[0][1] + strlen($lastMatch[0][0]);

                // 构建新的语言变量
                $newVars = '';
                foreach ($missingLang as $key) {
                    $value = $key;
                    $newVars .= ",\n    '{$key}' => '{$value}'";
                }

                // 插入新的语言变量
                $langContent = substr_replace($langContent, $newVars, $position, 0);

                // 写入文件
                file_put_contents($langFile, $langContent);

                $output->writeln('语言文件已更新，新增了 ' . count($missingLang) . ' 个语言变量：' . $langFile);
            } else {
                $output->writeln('无法找到合适的位置插入新的语言变量：' . $langFile);
            }
        } else {
            $output->writeln('没有发现缺失的语言变量：' . $langFile);
        }
    }

    /**
     * 标准化路径分隔符
     * 
     * 将路径中的各种分隔符（反斜杠、正斜杠）统一转换为正斜杠
     * 
     * @param string $dir 需要处理的路径字符串
     * @return string 处理后的标准化路径
     */
    protected function langDirSeparator($dir)
    {
        return str_replace(['\\', '\/', '/'], '/', $dir);
    }

    public function log($data)
    {
        $log['type'] = 'lang';
        $log['data'] = $data;
        trace($log, 'log');
    }
}
