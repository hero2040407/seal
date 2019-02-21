<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/24
 * Time: 10:23
 */
namespace app\push;

class Index
{
//    public function get(){
//        $content = "Uid:{$this->param['uid']};Name:test;say:{$this->param['msg']}";
//        $this->server->push($this->fd,$content);
//    }
    public function get(){
        $content = "FD:{$this->fd};say:{$this->param['msg']}";
        $this->task->delivery(\app\task\Notice::class,'ToAll',[$this->fd,$content]);
    }

    public function hello()
    {
        $content = "FD:{$this->fd};say:{$this->param['msg']}";
        $this->server->push($this->fd, $content);
    }
}