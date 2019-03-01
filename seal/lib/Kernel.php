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

class Kernel
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
            $content = self::exec($classname, $action);
            $response->header('Content-type', 'application/json');
            $response->end(json_encode($content, true));
        } catch (\Exception $e) {      //在此处返回 404错误的原因是因为加载器已经在查找不到文件时有说错误说明
            if ($e instanceof BaseException) {
                $content = [
                    'code' => $e->errorCode,
                    'msg' => $e->message,
                ];
                $response->header('Content-type', 'application/json');
                $response->end(json_encode($content, true));
                return;
            }
            Log::getInstance()->write('ERROR', $e->getFile(), $e->getLine(), $e->getMessage());
            $response->header('Content-type', "text/html;charset=utf-8;");
            $response->status(404);
            $response->end('404 NOT FOUND');
            return;
        } catch (\Throwable $e) {
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

    //执行反射类方法。

    /**
     * @param $class
     * @param $methodname
     * @throws
     */
    public static function exec($class, $method_name)
    {
        //注入的实质是通过php的反射类 来执行被注入类的行为
        //通过反射 可以得到类的一些信息 主要包括方法 属性
        //通过注入 我们可以实现类对方法和构造方法的依赖。下面我举例说明
        //1.先肯定是从一个类的构造函数开始。
        //2.利用反射得到这个类的构造函数 在执行
        $reflect = new \ReflectionClass($class);
        $constructor = $reflect->getConstructor();
        $p1 = $constructor != null ? self::getParamValue($constructor) : [];
            //执行被反射类的构造函数 实现构造函数的依赖注入

        $instance = $reflect->newInstanceArgs($p1);

        //执行注入类的方法。
        $reflect = new \ReflectionMethod($class, $method_name);
        $p2 = self::getParamValue($reflect);
        //执行方法注入。
        return $reflect->invokeArgs($instance, $p2);
    }

    //这个类用来 反射出当前注入类所在的参数。
    private static function getParamValue($reflect, $avgs = [])
    {
        if ($reflect->getNumberOfParameters() > 0) {
            foreach ($reflect->getParameters() as $param) {
                $param_type = $param->getClass();//获取当前注入对象的类型提示
                $param_value = $param->getName();//获取参数名称
                if ($param_type) {
                    //表示是对象类型的参数
                    $avgs[] = $param_type->name::getInstance();
                } else {
                    $avgs[] = $param_value;
                }
            }
        }
        return $avgs;
    }
}