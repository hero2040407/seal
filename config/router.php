<?php
/**
 * Created by PhpStorm.
 * UserModel: LENOVO
 * Date: 2019/1/16
 * Time: 11:39
 */

return [
    'module' => 'admin',     //默认模块
    'controller' => 'Index',     //默认控制器
    'action' => 'index',     //默认操作
    'ext' => '.htm',          //url后缀    例如 .html
    'rules' => [           //自定义路由
        'user' => 'uesr/index/init',
        'login' => 'index/login/init',
    ]
];