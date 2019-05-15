<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/5/7
 * Time: 下午12:18
 */

namespace seal\db\mysql;


use seal\Config;
use seal\db\Db;

class Model
{

    protected $autoTime = false;

    public function __construct()
    {

    }

    public static function getInstance()
    {
        return new static();
    }

    public static function get($id)
    {
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        $id = is_string($id) ? "'" . $id ."'" : $id;
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.prefix') . $table)
            ->where('id = ' . $id)->find()[0];
    }

    public function create()
    {
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        if ($this->autoTime) {
            $time = time();
            $this->data['create_time'] = $time;
            $this->data['update_time'] = $time;
            unset($time);
        }
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.prefix') . $table)
            ->insert($this->data);
    }

    public function update()
    {
        if (is_string($this->data['id'])) {
            $id = "'" . $this->data['id'] ."'";
        }

        $db = $this->getDb()->where('id=' . $id);
        if ($this->autoTime) {
            $this->data['update_time'] = time();
        }
        unset($this->data['id']);
        $db->update($this->data);
    }

    public function getDb() :Db
    {
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.prefix') . $table);
    }

    protected static function toUnderScore($str)
    {
        $str = str_replace('Model', '', $str);
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