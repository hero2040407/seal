<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/7/29
 * Time: 下午4:03
 */

namespace app\select\controller;

use app\select\model\Instruction;
use seal\Controller;
use seal\db\Db;

class Index extends Controller
{
    public function create(Instruction $instruction)
    {
        $instruction->name = 123;
        $instruction->instr = '测试的内容，说明我是成功执行了';
        $instruction->create();
        return $this->success($instruction->id);
    }

    public function index(Instruction $instruction)
    {
        $data = $instruction->index();
        return $this->success($data);
    }
}