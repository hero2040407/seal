<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/15
 * Time: 15:10
 */

define("APP_ROOT",dirname(__FILE__));

require "./seal/base.php";
require "common.php";
require 'vendor/autoload.php';

\seal\Seal::getInstance()->setConfig(\seal\Config::getInstance()->get('app'))->run();