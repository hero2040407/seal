<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 12:03
 */
return [
    //项目名称
    'name' => 'seal',
    //项目命名空间
    'namespace' => 'app',
    //项目所在路径
    'path' => realpath(__DIR__ . '/../app/'),
    //gzip 等级， 请查看  https://wiki.swoole.com/wiki/page/410.html
    'gzip' => 0,

    //server设置
    'ip' => '0.0.0.0',   //监听IP
    'port' => 9501,        //监听端口
    'server' => 'websocket',     //服务，可选 websocket 默认http

    //配置参数  请查看  https://wiki.swoole.com/wiki/page/274.html
    'set' => [
        'daemonize' => 0,
        'enable_static_handler' => TRUE,
        'document_root' => realpath(__DIR__ . '/../static/'),
        'worker_num' => 4,
        'max_request' => 10000,
        'task_worker_num' => 4,
    ],

    'log' => [
        //输出到屏幕，当 set.daemonize = false 时，该配置生效，
        'echo' => 0,
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别，共8个级别
        'level' => ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG', 'SQL'],
    ],
];