<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/16
 * Time: 12:11
 */
namespace app\admin;

use seal\Request;

class Index
{
    public function init()
    {
        echo  date('Y-m-d h:I:s'),'<br/>';
        echo __CLASS__;
    }

    public function hello(Request $request)
    {
        echo $request->uid;
    }
}