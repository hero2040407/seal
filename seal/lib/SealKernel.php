<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 10:56
 */

namespace seal;

use Swoole\Http\Response;

class SealKernel
{
    //实例
    private static $instance;
    private static $map;

    //防止被一些讨厌的小伙伴不停的实例化，自己玩。
    private function __construct()
    {
    }

    //还得让伙伴能实例化，并且能用它。。
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

//    public function http($request, $response)
//    {
//        $req = Request::getInstance();
//        $res = Router::getInstance()->http($request->server['request_uri']);
//        $req->set($request);
//        $response->end(var_export(Config::getInstance()->get('router.rules', true), TRUE));
//    }

    public function http($server, \Swoole\Http\Request $request, Response $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico') return;
        $req = Request::getInstance();
        $req->set($request);
        $router = Router::getInstance()->http($req->server['request_uri']);

        $app_namespace = Config::getInstance()->get('app.namespace');
        $module = $router['module'];
        $controller = ucfirst($router['controller']);
        $action = $router['action'];
        $param = $router['param'];
        $classname = "\\{$app_namespace}\\{$module}\\{$controller}";

        if (!isset(self::$map[$classname])) {
            $class = new $classname;
            self::$map[$classname] = $class;
        }
        try {
            //测试效果
            if (!empty(ob_get_contents())) ob_end_clean();
            ob_start();
            self::$map[$classname]->$action($req);
            $content = ob_get_contents();
            ob_end_clean();
            $response->end($content);
        } catch (\Exception $e) {      //在此处返回 404错误的原因是因为加载器已经在查找不到文件时有说错误说明
            $response->header('Content-type', "text/html;charset=utf-8;");
            $response->status(404);
            $response->end('404 NOT FOUND');
            return;
        }
    }

    public function websocket($server, $frame)
    {
        $router = Router::getInstance()->websocket($frame->data);

        $app_namespace = Config::getInstance()->get('app.namespace');
        $module = $router['m'];
        $controller = ucfirst($router['c']);
        $action = $router['a'];
        $param = $router['p'];

        $classname = "\\{$app_namespace}\\{$module}\\{$controller}";

        if (!isset(self::$map[$classname])) {
            try {
                $class = new $classname;
                self::$map[$classname] = $class;
            } catch (\Exception $e) {
                echo $e->getMessage(), PHP_EOL;
                return;
            }
        }
        try {
            self::$map[$classname]->server = $server;
            self::$map[$classname]->fd = $frame->fd;
            self::$map[$classname]->param = $param;
            self::$map[$classname]->task = Task::getInstance()->setServer($server);
            self::$map[$classname]->$action();
        } catch (\Exception $e) {
            echo $e->getMessage(), PHP_EOL;
            return;
        }
    }
}