<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/24
 * Time: 12:47
 */

namespace seal;

use Throwable;

class ExceptionHandle extends \Exception
{

    private static $instance;

    public function handler( Throwable $e) : void
    {
        Log::getInstance()->write('ERROR', $e->getFile(), $e->getLine(), $e->getMessage());
    }
//    public static function getInstance()
//    {
//        if (is_null(self::$instance)) {
//            self::$instance = new self();
//        }
//        return self::$instance;
//    }
//
//
//    public function handle(\Exception $e)
//    {
//
//    }
}