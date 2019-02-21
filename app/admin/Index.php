<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 12:11
 */
namespace app\admin;

use app\admin\model\Idea;
use seal\Push;
use seal\Request;

class Index
{
    public function hello(Request $request)
    {
//        $mysql = new \Swoole\Coroutine\MySQL(SWOOLE_SOCK_TCP);
//        $res = $mysql->connect([
//            'host' => '127.0.0.1',
//            'user' => 'root',
//            'password' => 'sos330635641',
//            'database' => 'idea',
//        ]);
//        if ($res == false) {
//            echo "MySQL connect fail!";
//            return;
//        }
//        $ret = $mysql->query("select * from pro_comment where id={$request->id}", 2);
//        print_r($ret);
//        $arr = [
//            '我的世界',
//            'abcdefg',
//            123321
//        ];
//        return $arr;
    }

    /**
     * @throws
     */
    public function log()
    {
//        Log::getInstance()->write('INFO', 'hello');
        throw new \Exception('catch me');
//        trigger_error('A custom error has been triggered');
    }

    public function sendToSingle(Request $request)
    {
        $fd = Push::getInstance()->get($request->uid);
        $content = "FD:{$fd['fd']};say:{$this->param['msg']}";
        $this->task->delivery(\app\task\Notice::class,'toSingle',[$fd['fd'], $content]);
    }

    /**
     * @param Idea $idea
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function index()
    {
        return (new Idea())->select();
    }
}