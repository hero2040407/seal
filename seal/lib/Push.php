<?php
/**
 * Created by PhpStorm.
 * UserModel: LENOVO
 * Date: 2019/1/15
 * Time: 16:10
 */

namespace seal;

use Swoole\Mysql\Exception;

class Push extends \swoole_table
{
    public static $instance;


    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            try {
                self::$instance = new self(65536);
                self::$instance->column('fd', Push::TYPE_INT, 4);       //1,2,4,8
                self::$instance->create();
            } catch (Exception $e) {
            }
        }
        return self::$instance;
    }

    public function deleteClient($fd)
    {
        foreach($this as $key => $value)
        {
            if ($fd == $value['fd']){
                $this->del($key);
                break;
            }
        }
    }

    public function getAllFds()
    {
        $fds = [];
        foreach($this as $item)
        {
            $fds[] = $item['fd'];
        }
        return $fds;
    }
}