<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/15
 * Time: 15:46
 */

//定义框架路径
define('SEAL_PATH', __DIR__ . '/');
define('CONFIG_PATH', dirname(__DIR__) . '/config/');
//引入加载器文件
require_once SEAL_PATH . "lib/Loader.php";
//注册它
\seal\Loader::register();