<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 10:56
 */

namespace seal;

use seal\exception\BaseException;
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
        try {
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

            self::$map[$classname]->server = $server;
            self::$map[$classname]->task = Task::getInstance()->setServer($server);

            //测试效果
            $content = self::$map[$classname]->$action($req);
            $response->header('Content-type','application/json');
            $response->end(json_encode($content, true));
        } catch (\Exception $e) {      //在此处返回 404错误的原因是因为加载器已经在查找不到文件时有说错误说明
                if ($e instanceof BaseException) {
                    $content = [
                        'code' => $e->errorCode,
                        'msg' => $e->message,
                    ];
                    $response->header('Content-type','application/json');
                    $response->end(json_encode($content, true));
                    return;
                }
                Log::getInstance()->write('ERROR', $e->getFile(), $e->getLine(), $e->getMessage());
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
//                ExceptionHandle::getInstance()->handle($e);
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
//            ExceptionHandle::getInstance()->handle($e);
            echo $e->getMessage(), PHP_EOL;
            return;
        }
    }
}