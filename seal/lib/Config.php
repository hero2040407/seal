<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 11:37
 */

namespace seal;

class Config
{
    /**
     * 实例
     * @var object
     */
    private static $instance;
    /**
     * @var array
     */
    private static $config = [];

    private function __construct()
    {
    }

    /**
     * @return object|Config
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取配置参数
     * @param string $keys 参数名 格式：文件名.参数名1.参数名2....
     * @param null $default 错误默认返回值
     * @return mixed|null
     */
    public function get($keys, $default = NULL)
    {

        $keys = array_filter(explode('.', strtolower($keys)));

        if (empty($keys)) return NULL;

        $file = array_shift($keys);

        if (empty(self::$config[$file])) {
            if (!is_file(CONFIG_PATH . $file . '.php')) {
                return NULL;
            }
            self::$config[$file] = include CONFIG_PATH . $file . '.php';
        }

        $config = self::$config[$file];

        while ($keys) {
            $key = array_shift($keys);
            if (!isset($config[$key])) {
                $config = $default;
                break;
            }
            $config = $config[$key];
        }

        return $config;
    }
}