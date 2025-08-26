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
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;
use Workerman\Crontab\Crontab as WCrontab;

/**
 * 定时任务Crontab：按cron执行任务
 * 调试模式启动：php think crontab
 * 守护进程启动：php think crontab -m d
 */
class Crontab extends Command
{
    protected $type = 'crontab';

    protected $crontab;

    protected function configure()
    {
        // 指令配置
        $this->setName('crontab')
            ->addArgument('action', Argument::OPTIONAL, 'start|stop|restart|reload|status|connections', 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, '守护进程方式启动（-m d）')
            ->setDescription('定时任务 Crontab');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $mode = $input->getOption('mode');

        // 重新构造命令行参数,以便兼容workerman的命令
        global $argv;
        $argv = [];
        array_unshift($argv, 'think', $action);
        if ($mode == 'd') {
            $argv[] = '-d';
        } else if ($mode == 'g') {
            $argv[] = '-g';
        }
        $this->log(implode(' ', $argv));
        // 指令输出
        $output->writeln(__CLASS__ . '：' . implode(' ', $argv));
        Worker::$logFile = runtime_path() . '/log/workerman.log';

        // 设置时区，避免运行结果与预期不一致
        date_default_timezone_set('PRC');

        $worker1 = new Worker();
        $worker1->onWorkerStart = function () {
            $crontab_name = 'log-clear'; // 名称，用于调试输出显示
            $crontab_rule = '* * * * *'; // cron表达式：每分钟执行
            $crontab_msg  =  $crontab_rule . ' ' . $crontab_name;
            $this->output($crontab_msg, 'start');
            $this->crontab[] = new WCrontab($crontab_rule, function () use ($crontab_msg) {
                $this->output($crontab_msg, 'run');
                // 这里执行具体的任务
                event('LogClear'); // 日志清除
            });
        };

        $worker2 = new Worker();
        $worker2->onWorkerStart = function () {
            $crontab_name = ''; // 名称，用于调试输出显示
            $crontab_rule = '0 1 * * *'; // cron表达式：每天凌晨1点整执行
            $crontab_msg  = $crontab_rule . ' ' . $crontab_name;
            $this->output($crontab_msg, 'start');
            $this->crontab[] = new WCrontab($crontab_rule, function () use ($crontab_msg) {
                $this->output($crontab_msg, 'run');
                // 这里执行具体的任务
            });
        };

        Worker::runAll();
    }

    public function stop()
    {
        foreach ($this->crontab as $id) {
            WCrontab::remove($id);
        }
        global $argv;
        $this->log(implode(' ', $argv));
    }

    protected function output($msg, $status = '')
    {
        $type = $this->type;
        $msgs = [datetime('', true), $type, $status, $msg];
        $msgs = implode(' ', $msgs);
        $msgs = preg_replace('/\s+/', ' ', $msgs);
        echo $msgs . PHP_EOL;
    }

    protected function log($data)
    {
        $log['type'] = $this->type;
        $log['data'] = $data;
        trace($log, 'log');
    }
}
