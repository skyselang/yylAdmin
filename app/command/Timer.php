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
use Workerman\Timer as WTimer;

/**
 * 定时任务Timer：按间隔执行任务
 * 调试模式启动：php think timer
 * 守护进程启动：php think timer -m d
 */
class Timer extends Command
{
    protected $type = 'timer';

    protected $timer;

    protected function configure()
    {
        // 指令配置
        $this->setName('timer')
            ->addArgument('action', Argument::OPTIONAL, 'start|stop|restart|reload|status|connections', 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, '守护进程方式启动（-m d）')
            ->setDescription('定时任务 Timer');
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

        $worker1 = new Worker();
        $worker1->onWorkerStart = function () {
            $timer_name     = 'log-clear'; // 名称，用于调试输出显示
            $timer_interval = 5; // 多长时间执行一次，单位秒
            $timer_msg      = $timer_interval . 's ' . $timer_name;
            $this->output($timer_msg, 'start');
            $this->timer[] = WTimer::add($timer_interval, function () use ($timer_msg) {
                $this->output($timer_msg, 'runing');
                // 这里执行具体的任务
                event('LogClear'); // 日志清除
            });
        };

        $worker2 = new Worker();
        $worker2->onWorkerStart = function () {
            $timer_name     = ''; // 名称，用于调试输出显示
            $timer_interval = 10; // 多长时间执行一次，单位秒
            $timer_msg      = $timer_interval . 's ' . $timer_name;
            $this->output($timer_msg, 'start');
            $this->timer[] = WTimer::add($timer_interval, function () use ($timer_msg) {
                $this->output($timer_msg, 'runing');
                // 这里执行具体的任务
            });
        };

        Worker::runAll();
    }

    public function stop()
    {
        foreach ($this->timer as $id) {
            WTimer::del($id);
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
