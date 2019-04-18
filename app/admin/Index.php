<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 12:11
 */
namespace app\admin;

use app\admin\model\Idea;
use app\admin\service\Beanstalk;
use seal\db\Db;
use seal\exception\ResultException;
use seal\Push;
use seal\Request;
use Swoole\Coroutine;
use Swoole\Coroutine\MySQL;

class Index
{
//    /admin/index/hello?hello=1
    public function hello(Request $request)
    {
        $db = Db::getInstance();
        $mysql = $db->getConnection();
        $res = $mysql->query("select * from pro_menu where id=2");
        $db->close($mysql);
        return $res;
//        $db->close($mysql);
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
        throw new ResultException('catch me');
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
    public function index(Request $request)
    {
//        echo 123;
//        echo $request->hello;
//        return $idea->paginate();
    }
}