<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/15
 * Time: 16:10
 */
namespace seal;

use Swoole\Mysql\Exception;

class Push
{
    private $server;
    public static $instance;
    private static $list;

    /**
     * Push constructor.
     * @param \swoole_websocket_server $ws
     * @throws \Swoole\Mysql\Exception
     */
    public function __construct(\swoole_websocket_server $ws)
    {
        $this->server = $ws;
//        $db = new MySQL();
//        $server = array(
//            'host' => '127.0.0.1',
//            'user' => 'root',
//            'password' => 'sos330635641',
//            'database' => 'idea',
//        );
//        $db->connect($server, function (MySQL $db, $result) {
//            $db->query("select * from pro_idea", function (MySQL $db, $result) {
//                var_dump($result);
//            });
//        });
    }

    public static function getInstance(\swoole_websocket_server $ws)
    {
        if (!self::$instance){
            try {
                self::$instance = new self($ws);
            } catch (Exception $e) {
            }
        }
        return self::$instance;
    }

    public function send($frame, $data = '')
    {
        if (!$data)
            $data = 'hello';
        $this->server->push($frame->fd, $data);
    }

    public function addClient($fd, $uid)
    {
        self::$list[$uid] = $fd;
    }
    
    public function deleteClient($fd)
    {
        $uid = array_search($fd, self::$list);
        unset(self::$list[$uid]);
    }

    public function getAllClient()
    {
        var_dump(self::$list);
    }
}