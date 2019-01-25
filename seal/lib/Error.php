<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/24
 * Time: 14:55
 */
namespace seal;

use seal\exception\ErrorException;
use seal\exception\ThrowableError;


class Error
{
    /**
     * 配置参数
     * @var array
     */
    protected static $exceptionHandler;

    /**
     * 注册异常处理
     * @access public
     * @return void
     */
    public static function register()
    {
//        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
//        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * Exception Handler
     * @access public
     * @param  \Exception|\Throwable $e
     */
    public static function appException($e)
    {
        if (!$e instanceof \Exception) {
            $e = new ThrowableError($e);
        }
        Log::getInstance()->write('ERROR', $e->getMessage() . "\r\n",
            $e->getFile(), $e->getLine());
//        self::getExceptionHandler()->report($e);
//
//        if (PHP_SAPI == 'cli') {
//            self::getExceptionHandler()->renderForConsole(new ConsoleOutput, $e);
//        } else {
//            self::getExceptionHandler()->render($e)->send();
//        }
    }

    /**
     * Error Handler
     * @access public
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline);
        if (error_reporting() & $errno) {
            // 将错误信息托管至 think\exception\ErrorException
            throw $exception;
        }
        Log::getInstance()->write('ERROR', $exception->getMessage() . "\r\n",
            $exception->getFile(), $exception->getLine());
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * 设置异常处理类
     *
     * @access public
     * @param  mixed $handle
     * @return void
     */
    public static function setExceptionHandler($handle)
    {
        self::$exceptionHandler = $handle;
    }
}
