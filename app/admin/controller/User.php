<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/5/15
 * Time: 下午2:48
 */

namespace app\admin\controller;


use app\admin\model\UserModel;
use seal\Request;

class User extends Base
{
    protected $beforeAction = ['isLogin' => ['update']];

    public function read(Request $request)
    {
        $user = UserModel::get($request->uid);
        return $this->success($user);
    }

    public function update(UserModel $user, Request $request)
    {
        $arr = ['mobile', 'resume'];
        foreach ($arr as $value) {
            if (isset($request->$value)) {
                $user->$value = $request->$value;
            }
        }
        $user->id = $this->getUserToken();
        $user->update();
        return $this->success();
    }
}