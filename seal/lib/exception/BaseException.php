<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/28
 * Time: 11:53
 */
namespace seal\exception;

class BaseException extends \Exception
{
    public $message = 'invalid parameters';
    public $errorCode = 200;
    public $shouldToClient = true;

    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含code、msg和errorCode，且不应该是空值
     */
    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return;
        }
        if (array_key_exists('msg', $params)) {
            $this->message = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}
