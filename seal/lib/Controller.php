<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/7/29
 * Time: 下午4:01
 */

namespace seal;


class Controller
{
    protected $beforeAction = [];

    public function beforeAction($method, $options = [])
    {
        if (empty($options)) {
            call_user_func([$this, $method]);
        }
        else {
            $action = Request::getInstance()->getAction();
            if (in_array($action, $options)) {
                call_user_func([$this, $method]);
            }
        }
    }

    /**
     * @return array
     */
    public function getBeforeAction(): array
    {
        return $this->beforeAction;
    }

    public function success($data = '')
    {
        $arr = [
            'code' => 1,
            'data' => $data,
            'msg' => '请求成功'
        ];

        return $arr;
    }
}