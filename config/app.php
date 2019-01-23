<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 12:03
 */
return [
    'name' => 'seal',                            //项目名称
    'namespace' => 'app',                               //项目命名空间
    'path' => realpath(__DIR__ . '/../app/'),  //项目所在路径
    'gzip' => 0,                                    //gzip 等级， 请查看  https://wiki.swoole.com/wiki/page/410.html

    //server设置
    'ip' => '0.0.0.0',   //监听IP
    'port' => 9501,        //监听端口
    'server' => 'websocket',     //服务，可选 websocket 默认http

    'set' => [            //配置参数  请查看  https://wiki.swoole.com/wiki/page/274.html
        'daemonize' => 0,
        'enable_static_handler' => TRUE,
        'document_root' => realpath(__DIR__ . '/../static/'),
        'worker_num' => 4,
        'max_request' => 10000,
        'task_worker_num' => 4,
    ],
];