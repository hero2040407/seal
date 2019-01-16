<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 11:39
 */

return [
    'm'             => 'index',     //默认模块
    'c'             => 'index',     //默认控制器
    'a'             => 'init',     //默认操作
    'ext'           => '.html',          //url后缀    例如 .html
    'rules'         =>  [           //自定义路由
        'user'  => 'uesr/index/init',
        'login' => 'index/login/init',
    ]
];