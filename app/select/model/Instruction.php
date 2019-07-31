<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/7/29
 * Time: 下午5:02
 */

namespace app\select\model;

use seal\db\mysql\Model;

class Instruction extends Model
{

    public function index()
    {
        $data = $this->getDb()->paginate();
        $this->getDb()->parent($data['data'],
            InstructionItem::class,
            'instr_id', 'instr_item');
        return $data;
    }
}