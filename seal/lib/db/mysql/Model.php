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
use seal\Request;

class Model
{

    protected $autoTime = false;
    protected $updateTime = false;
    private $data;

    public function __construct()
    {

    }

    public static function getInstance()
    {
        return new static();
    }

    public static function get($id)
    {
        $module = Request::getInstance()->getModule();
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        $id = is_string($id) ? "'" . $id ."'" : $id;
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.'.$module.'.prefix') . $table)
            ->where('id=' . $id)->find();
    }

    public function create()
    {
        $module = Request::getInstance()->getModule();
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        if ($this->autoTime) {
            $this->data['create_time'] = time();
            if ($this->updateTime)
                $this->data['update_time'] = time();
        }
        $db = Db::getInstance()->table(Config::getInstance()
                ->get('database.'.$module.'.prefix') . $table);

        $this->id = $db->insert($this->data);
    }

    public function update()
    {
        $id = $this->exchangeId($this->data['id']);
        $db = $this->getDb()->where('id=' . $id);
        if ($this->autoTime && $this->updateTime) {
            $this->data['update_time'] = time();
        }
        unset($this->data['id']);
        $db->update($this->data);
    }

    public function getDb() :Db
    {
        $module = Request::getInstance()->getModule();
        $table = static::toUnderScore(basename(str_replace('\\', '/', static::class)));
        return Db::getInstance()->table(Config::getInstance()
                ->get('database.'.$module.'.prefix') . $table);
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

    public function __get($name)
    {
        return $this->data[$name];
    }

    private function exchangeId($value)
    {
        if (is_string($value)) {
            return "'" . $value ."'";
        }
        return $value;
    }
}