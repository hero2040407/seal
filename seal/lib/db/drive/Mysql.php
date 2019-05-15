<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/3/29
 * Time: 下午1:52
 */
namespace seal\db\drive;



trait Mysql
{

    public $sql;
    private $table;
    private $field = '*';
    private $where = '';
    private $order;


    public function get($id)
    {
        $this->sql = 'select * from';
        return $this;
    }

    public function field($field)
    {
        $this->field = $field;
        return $this;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function where($where = '')
    {
        $this->where = ' where ' . $where;
        return $this;
    }

    public function order($order = '')
    {
        $this->order = ' order by ' . $order;
        return $this;
    }

    public function select()
    {
        $this->sql = 'select '.
            $this->field.
            ' from '.
            $this->table;
        if ($this->where) {
            $this->sql .= $this->where;
        }
        if ($this->order) {
            $this->sql .= $this->order;
        }
        return $this->achieve();
    }

    public function find()
    {
        $this->sql = 'select '.
            $this->field.
            ' from '.
            $this->table;
        if ($this->where) {
            $this->sql = $this->sql . $this->where;
        }
        $this->sql .= ' limit 1';
        return !empty($this->achieve()) ? $this->achieve()[0] : false;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function insert($data = [])
    {
        $this->sql = 'INSERT INTO '. $this->table;
        $keys  = array_keys($data);
        $this->sql   .= ' (' . implode(',', $keys) . ')';
        $build = array_values($data);
        foreach ($build as &$value) {
            if (!is_numeric($value))
                $value = "'$value'";
        }
        $this->sql   .= ' VALUES (' . implode(',', $build) . ')';
        return $this->achieve();
    }

    public function update($data = [])
    {
        $this->sql = 'UPDATE '. $this->table . ' SET ';

        foreach ($data as $key => $value) {
            if (!is_numeric($value))
                $value = "'$value'";
            $build[] = "$key=$value";
        }
        $this->sql   .=  implode(',', $build) . $this->where;
        return $this->achieve();
    }
}