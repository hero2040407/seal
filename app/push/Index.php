<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/24
 * Time: 10:23
 */
namespace app\push;

use seal\Push;

class Index
{
//    public function get(){
//        $content = "Uid:{$this->param['uid']};Name:test;say:{$this->param['msg']}";
//        $this->server->push($this->fd,$content);
//    }
    public function sendToAll(){
        $arr = ['msg' => $this->param['msg']];
        $content = json_encode($arr);
        $this->task->delivery(\app\task\Notice::class,'toAll',[$this->fd,$content]);
    }

    public function hello()
    {
        $fd = Push::getInstance()->get($this->param['uid'])['fd'];
        $content = "FD:{$fd};say:{$this->param['msg']}";
        $this->server->push($fd, $content);
    }

    public function getAll()
    {
        $count = Push::getInstance()->count();
        $this->server->push($this->fd, $count);
    }
}