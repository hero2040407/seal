<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/3/6
 * Time: 下午1:20
 */

namespace app\admin\service;

use Pheanstalk\Pheanstalk;

class Beanstalk extends Pheanstalk
{
    private static $instance;

    public static function getInstance() :Beanstalk
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

}