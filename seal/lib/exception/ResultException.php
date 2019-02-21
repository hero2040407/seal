<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/28
 * Time: 11:59
 */
namespace seal\exception;

class ResultException extends BaseException
{
    public $errorCode = 0;
    public $msg = "通用错误处理";
}