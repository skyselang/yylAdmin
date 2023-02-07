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
use app\common\service\member\LogService;
use app\common\service\system\UserLogService;

/**
 * 定时任务
 */
class Timer extends Command
{
    /**
     * 定时器
     */
    protected $timer;

    /**
     * 多长时间执行一次
     * @var integer
     */
    protected $interval = 5;

    protected function configure()
    {
        // 指令配置
        $this->setName('timer')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, 'daemon（守护进程）方式启动')
            ->addOption('interval', 'i', Option::VALUE_OPTIONAL, '多长时间执行一次')
            ->setDescription('the timer command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('timer:execute');

        $this->init($input, $output);
        $worker = new Worker();
        $worker->onWorkerStart = [$this, 'start'];
        $worker->runAll();
    }

    protected function init(Input $input, Output $output)
    {
        $output->writeln('timer:init');

        global $argv;
        $argv = [];

        $action = $input->getArgument('action');
        $mode = $input->getOption('mode');
        $interval = $input->getOption('interval');
        if ($interval) {
            $this->interval = floatval($interval);
        }

        array_unshift($argv, 'think', $action);
        if ($mode == 'd') {
            $argv[] = '-d';
        } else if ($mode == 'g') {
            $argv[] = '-g';
        }

        $output->writeln('argv:' . implode(',', $argv));
    }

    public function stop()
    {
        \Workerman\Lib\Timer::del($this->timer);
    }

    public function start()
    {
        $this->timer = \Workerman\Lib\Timer::add($this->interval, function () {
            echo 'timer start ' . date('Y-m-d H:i:s') . PHP_EOL;
            // 会员日志清除
            LogService::clearLog();
            // 用户日志清除
            UserLogService::clearLog();
        });
    }
}
