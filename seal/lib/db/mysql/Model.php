<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/5/7
 * Time: 下午12:18
 */

namespace seal\db\mysql;


use seal\Config;
use seal\db\Db;

class Model
{

    public static function get($id)
    {
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.prefix') . $table)
            ->where('id = ' . $id)->find()[0];
    }

    public static function save($data)
    {
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        if (isset($data['id'])) {
            return Db::getInstance()->table(Config::getInstance()
                    ->get('database.prefix') . $table)
                ->update($data);
        }
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.prefix') . $table)
            ->insert($data);
    }

    protected static function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
            return '_' . strtolower($matchs[0]);
        }, $str);
        return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}