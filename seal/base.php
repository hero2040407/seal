<?php
/**
 * Created by PhpStorm.
 * UserModel: LENOVO
 * Date: 2019/1/15
 * Time: 15:46
 */

//定义框架路径
define('SEAL_PATH', __DIR__ . '/');
define('CONFIG_PATH', dirname(__DIR__) . '/config/');
define('LOG_PATH', dirname(__DIR__) . '/logs/');

//引入加载器文件
require_once SEAL_PATH . "lib/Loader.php";
require_once SEAL_PATH . "lib/Config.php";
//注册它
\seal\Loader::register();

class start
{
    /**
     * 配置参数 config/app.php
     * @var array
     */
    private static $config = null;

    /**
     * frame/Lib/Server.php 实例
     * @var null
     */
    protected static $worker = null;

    public static function run($opt = NULL)
    {

        $config = \seal\Config::getInstance();

        if (version_compare(phpversion(), '7.1', '<')) {
            echo "PHP版本必须大于等于7.1 ，当前版本：", phpversion(), PHP_EOL;
            die;
        }

        if (version_compare(phpversion('swoole'), '2.1', '<')) {
            echo "Swoole 版本必须大于等于 2.1 ，当前版本：", phpversion('swoole'), PHP_EOL;
            die;
        }
        if (php_sapi_name() != "cli") {
            echo "仅允许在命令行模式下运行", PHP_EOL;
            die;
        }
        //检查命令
        if (!in_array($opt, ['start', 'stop', 'kill', 'restart', 'reload'])) {
            echo PHP_EOL, "Usage:", PHP_EOL, "     php start.php [start|stop|kill|restart|reload]", PHP_EOL, PHP_EOL;
            die;
        }

        self::$config = $config->get('app');
        //注册项目命名空间和路径
        \seal\Loader::addNamespace($config->get('app.namespace'), $config->get('app.path'));
        \seal\Error::register();
        //检查日志目录是否存在并创建
        !is_dir(LOG_PATH) && mkdir(LOG_PATH, 0777, TRUE);
        //检查是否配置app.name
        if (empty(self::$config['name'])) {
            echo "配置项 config/app.php [name] 不可留空 ", PHP_EOL;
            die;
        }

        $app_name = self::$config['name'];

        //获取master_pid 关闭或重启时要用到
        $master_pid = exec("ps -ef | grep {$app_name}-master | grep -v 'grep ' | awk '{print $2}'");
        //获取manager_pid 重载时要用到
        $manager_pid = exec("ps -ef | grep {$app_name}-manager | grep -v 'grep ' | awk '{print $2}'");

        if (empty($master_pid)) {
            $master_is_alive = false;
        } else {
            $master_is_alive = true;
        }

        if ($master_is_alive) {
            if ($opt === 'start') {
                echo "{$app_name}  正在运行", PHP_EOL;
                exit;
            }
        } elseif ($opt !== 'start') {
            echo "{$app_name} 未运行", PHP_EOL;
            exit;
        }

        switch ($opt) {
            case 'start':
                break;
            case "kill":
                //代码参考 https://wiki.swoole.com/wiki/page/233.html
                exec("ps -ef|grep {$app_name}|grep -v grep|cut -c 9-15|xargs kill -9");
                break;

            case 'stop':
                echo "{$app_name}  正在停止 ...", PHP_EOL;
                // 发送SIGTERM信号，主进程收到SIGTERM信号时将停止fork新进程，并kill所有正在运行的工作进程
                // 详见 https://wiki.swoole.com/wiki/page/908.html
                $master_pid && posix_kill($master_pid, SIGTERM);
                // Timeout.
                $timeout = 40;
                $start_time = time();

                while (1) {                           //强制退出
                    $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                    if ($master_is_alive) {
                        if (time() - $start_time >= $timeout) {
                            echo "{$app_name} 停止失败", PHP_EOL;
                            exit;
                        }
                        usleep(10000);
                        continue;
                    }
                    echo "{$app_name} 已停止", PHP_EOL;
                    break;
                }
                exit(0);
                break;
            case 'reload':
                //详见：https://wiki.swoole.com/wiki/page/20.html
                // SIGUSR1: 向主进程/管理进程发送SIGUSR1信号，将平稳地restart所有worker进程
                posix_kill($manager_pid, SIGUSR1);
                echo "[SYS]", "\t", "{$app_name} 重载", PHP_EOL;
                exit;
            case 'restart':
                echo "{$app_name} 正在停止", PHP_EOL;
                // 发送SIGTERM信号，主进程收到SIGTERM信号时将停止fork新进程，并kill所有正在运行的工作进程
                // 详见 https://wiki.swoole.com/wiki/page/908.html
                $master_pid && posix_kill($master_pid, SIGTERM);
                $timeout = 40;
                $start_time = time();
                while (1) {
                    //检查master_pid是否存在
                    $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                    if ($master_is_alive) {
                        if (time() - $start_time >= $timeout) {
                            echo "{$app_name} 停止失败", PHP_EOL;
                            exit;
                        }
                        usleep(10000);
                        continue;
                    }
                    echo "{$app_name} 已停止", PHP_EOL;
                    break;
                }

                break;
        }

        self::$worker = \seal\Seal::getInstance();
        self::$worker->setConfig(self::$config);
        echo "{$app_name}", '启动成功', PHP_EOL;
        self::$worker->run();
    }
}