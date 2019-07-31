<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/3/29
 * Time: 下午1:52
 */

namespace seal\db\drive;


use seal\Request;

trait Mysql
{

    public $sql;
    private $table;
    private $field = '*';
    private $where = '';
    private $order;

    public function clear()
    {
        $this->sql = '';
        $this->field = '*';
        $this->where = '';
        $this->order = null;
    }

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
        $this->sql = 'select ' .
            $this->field .
            ' from ' .
            $this->table;
        if ($this->where) {
            $this->sql .= $this->where;
        }
        if ($this->order) {
            $this->sql .= $this->order;
        }
        $data = $this->achieve();
        $this->close();
        return $data;
    }

    public function find()
    {
        $this->sql = 'select ' .
            $this->field .
            ' from ' .
            $this->table;
        if ($this->where) {
            $this->sql = $this->sql . $this->where;
        }
        $this->sql .= ' limit 1';
        $data = $this->achieve();
        $result = !empty($data) ? $data[0] : false;
        $this->close();
        return $result;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function insert($data = [])
    {
        $this->sql = 'INSERT INTO ' . $this->table;
        $keys = array_keys($data);
        $this->sql .= ' (' . implode(',', $keys) . ')';
        $build = array_values($data);
        foreach ($build as &$value) {
            if (!is_numeric($value))
                $value = "'$value'";
        }
        $this->sql .= ' VALUES (' . implode(',', $build) . ')';

        $this->achieve();
        $this->getLastId();
        $id = $this->achieve()[0];
        $this->close();
        return $id;
    }

    public function getLastId()
    {
        $this->sql = 'SELECT LAST_INSERT_ID() as id';
    }

    /**
     * @param array $data
     * @return $this
     */
    public function insertAll($data = [])
    {
        $this->sql = 'INSERT INTO ' . $this->table;
        $keys = array_keys($data[0]);
        $this->sql .= ' (' . implode(',', $keys) . ')';
        $insert_data = '';
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $build = array_values($data[$i]);
            foreach ($build as &$value) {
                if (!is_numeric($value))
                    $value = "'$value'";
            }
            if ($i < $count - 1)
                $insert_data .= '(' . implode(',', $build) . '),';
            else
                $insert_data .= '(' . implode(',', $build) . ')';
        }
        $this->sql .= ' VALUES' . $insert_data;
        return $this->achieve();
    }

    public function update($data = [])
    {
        $this->sql = 'UPDATE ' . $this->table . ' SET ';

        foreach ($data as $key => $value) {
            if (!is_numeric($value))
                $value = "'$value'";
            $build[] = "$key=$value";
        }
        $this->sql .= implode(',', $build) . $this->where;
        $result =  $this->achieve();
        $this->close();
        return $result;
    }

    public function paginate($row_num = 10)
    {
        $page = Request::getInstance()->page ?? 1;
        $this->sql = 'select ' .
            $this->field .
            ' from ' .
            $this->table;
        if ($this->where) {
            $this->sql .= $this->where;
        }
        if ($this->order) {
            $this->sql .= $this->order;
        }
        $this->sql .= ' limit ' . ($page - 1) * $row_num . ",$row_num";
        $arr['data'] = $this->achieve();
        $this->sql = 'select count(*) as count from ' . $this->table;
        if ($this->where) {
            $this->sql .= $this->where;
        }
        $arr['total'] = (int)$this->achieve()[0]['count'];
        $arr['lastPage'] = ceil($arr['total'] / $row_num);
        $this->close();
        return $arr;
    }

    public function parent(&$parent_data, $child_model, $foreign_id, $field)
    {
        foreach ($parent_data as $datum) {
            $ids[] = $datum['id'];
        }
        $child_data = $child_model::getInstance()->getDb()
            ->where($foreign_id .' in (' .implode(',', $ids).')')->select();
        foreach ($parent_data as &$parent_datum) {
            $parent_datum[$field] = '';
            foreach ($child_data as $child_datum) {
                if ($child_datum[$foreign_id] == $parent_datum['id']) {
                    $parent_datum[$field] = $child_datum;
                }
            }
        }
    }

    public function child($parent_data, &$child_data, $foreign_id, $field)
    {
        foreach ($parent_data as $parent_datum) {
            foreach ($child_data as &$child_datum) {
                if ($child_datum[$foreign_id] == $parent_datum['id']) {
                    $child_datum[$field] = $parent_datum;
                }
            }
        }
    }
}