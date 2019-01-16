<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 11:16
 */

namespace seal;

class Router
{
    /**实例
     * @var object
     */
    private static $instance;
    //默认配置
    private static $config = [
        'm' => 'index',     //默认模块
        'c' => 'index',     //默认控制器
        'a' => 'init',     //默认操作
        'ext' => '.html',          //url后缀    例如 .html
        'rules' => [           //自定义路由
            'user' => 'user/index/init',
            'login' => 'index/login/init',
        ]
    ];

    private function __construct()
    {
    }

    /**
     * 获取实例
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$config = Config::getInstance()->get('router');
        }
        return self::$instance;
    }

    /**
     * 这里就要一个参数 swoole_http_server->request->server[request_uri]
     */
    public function http($request_uri)
    {
        $param = [];
        $module = self::$config['m'];
        $controller = self::$config['c'];
        $action = self::$config['a'];

        if (empty($request_uri)) {
            return ['m' => $module, 'c' => $controller, 'a' => $action, 'p' => $param];
        }

        $path = trim($request_uri, '/');
        if (!empty(self::$config['ext'])) {
            $path = rtrim($path, self::$config['ext']);
        }

        if (!empty(self::$config['rules'])) {
            foreach (self::$config['rules'] as $key => $value) {
                if (substr($path, 0, strlen($key)) == $key) {
                    $path = str_replace($key, $value, $path);
                    break;
                }
            }
        }

        $param = explode("/", $path);
        !empty($param[0]) && $module = $param[0];
        isset($param[1]) && $controller = $param[1];
        isset($param[2]) && $action = $param[2];

        if (count($param) >= 3) {
            $param = array_slice($param, 3);
        } else {
            $param = array_slice($param, 2);
        }
        $params = array();
        foreach ($param as $key => $value) {
            if ($key % 2 === 0) {
                $params[$value] = $param[$key + 1];
            }
        }
        return ['m' => $module, 'c' => $controller, 'a' => $action, 'p' => $params];
    }
}