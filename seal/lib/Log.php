<?php
/**
 * 日志
 */

namespace seal;

use Swoole\Coroutine;

class Log
{
    /**
     * 实例
     * @var object
     */
    private static $instance;
    /**
     * 配置参数
     * @var array
     */
    private static $config = [];

    private static $logs = [];


    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$config = Config::getInstance()->get('app.log');
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $type
     * @param mixed ...$logs
     * @return bool
     * @throws
     */
    public function write($type, ...$logs)
    {
        $type = strtoupper($type);
        $msg = "{$type} \t " . date("Y-m-d h:i:s") . " \t " . join(" \t ", $logs);
        if (!in_array($type, self::$config['level'])) return false;
        if (self::$config['echo']) {
            echo $msg, PHP_EOL;
        }
        self::$logs[$type][] = $msg;
    }

    public function save()
    {
        if (empty(self::$logs)) return false;
        $dir_path = LOG_PATH . 'daily' . DIRECTORY_SEPARATOR;
        !is_dir($dir_path) && mkdir($dir_path, 0777);
        $filename = date('md')  . '.log';
        $log_content = '';
        foreach (self::$logs as $type => $logs) {
//            $dir_path = LOG_PATH . date('Ymd') . DIRECTORY_SEPARATOR;

            $content = NULL;
            foreach ($logs as $log) {
                $content .= $log . PHP_EOL . "\n";
            }
            $log_content = $log_content.$content . "\r\n";
        }
        Coroutine::writeFile($dir_path . $filename, $log_content,FILE_APPEND);
        self::$logs = [];
        return true;
    }
}
