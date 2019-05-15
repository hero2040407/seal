<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/5/10
 * Time: 上午7:51
 */

namespace seal;


use Swoole\Coroutine\Http\Client;

class Http
{

    public static function get($domain, $path, $port = 80, $ssl = false)
    {
        $cli = new Client($domain, $port, $ssl);
        $cli->get($path);
        $res = json_decode($cli->body);
        if ($cli->statusCode !== 200) {
            $res = socket_strerror($cli->errCode);
        }
        $cli->close();
        return $res;
    }

    public static function post($domain, $path, $data, $port = 80, $ssl = false)
    {
        $cli = new Client($domain, $port, $ssl);
        $cli->post($path, $data);
        $res = json_decode($cli->body);
        if ($cli->statusCode !== 200) {
            $res = socket_strerror($cli->errCode);
        }
        $cli->close();
        return $res;
    }
}