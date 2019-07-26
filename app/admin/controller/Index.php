<?php
/**
 * Created by PhpStorm.
 * UserModel: LENOVO
 * Date: 2019/1/16
 * Time: 12:11
 */
namespace app\admin\controller;

use app\admin\model\FormModel;
use seal\db\Db;
use seal\exception\ResultException;
use seal\Push;
use seal\Request;

class Index extends Base
{
    protected $beforeAction = ['before' => ['index']];

    public function hello(Db $db, Request $request)
    {

        $res = $db->table('pro_menu')->order('id desc')
            ->where('id>5')->select();
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

    public function sendToAll(Request $request)
    {
        $fd = Push::getInstance()->get($request->uid);
        $content = "FD:{$fd['fd']};say:{$request->message}";
        $this->task->delivery(\app\task\Notice::class,'toAll',[$fd['fd'], $content]);
    }

    public function notify()
    {
        $fds = Push::getInstance()->getAllFds();
        $content = 123321123;
        $this->task->delivery(\app\task\Notice::class,'notify',[$fds, $content]);
//        echo count($this->server->connections);
        return $this->success($fds);
    }

    /**
     * @param Idea $idea
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function index(Request $request)
    {

    }

    public function before()
    {
        return 'this is beforeAction';
    }

    public function getFormIndex(FormModel $formModel)
    {
        go(function () use ($formModel){
            $formModel->name = 123;
            $formModel->create();
        });
        return $this->success();
    }
}