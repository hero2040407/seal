<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/3/28
 * Time: 下午3:00
 */

namespace seal\db;

class Db
{
    use \seal\db\drive\Mysql;

    public $connection;

    public static function getInstance()
    {
        $db = new self();
        $db->connection = MysqlPool::getInstance()->getConnection();
        return $db;
    }

    public function achieve()
    {
        try {
            $arr = $this->connection->query($this->sql);
            echo $this->connection->error;
            $this->clear();
            return $arr;
        } catch (\Exception $e) {
            $this->connection = MysqlPool::getInstance()->getConnection();
            return $this->achieve();
        }
    }

    public function close()
    {
        MysqlPool::getInstance()->close($this);
    }
}