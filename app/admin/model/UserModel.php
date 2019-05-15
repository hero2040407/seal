<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/5/9
 * Time: 上午10:54
 */

namespace app\admin\model;


use seal\db\mysql\Model;

class UserModel extends Model
{

    protected $autoTime = true;

    public function setId()
    {
        $this->id = uniqid(rand(10000,99999));
    }
}