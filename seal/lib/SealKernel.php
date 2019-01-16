<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 10:56
 */

namespace seal;


class SealKernel
{
    //实例
    private static $instance;

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

    public function http($request, $response)
    {
        $req = Request::getInstance();
        $res = Router::getInstance()->http($request->server['request_uri']);
        $req->set($request);
        $response->end(var_export (Config::getInstance()->get('router.rules', true),TRUE));
    }
}