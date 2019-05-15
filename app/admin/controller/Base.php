<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/5/14
 * Time: 下午3:36
 */

namespace app\admin\controller;


use seal\exception\ResultException;
use seal\Request;

class Base
{
    protected $userToken;
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

    /**
     * @return mixed
     */
    public function getUserToken()
    {
        return Request::getInstance()->header['usertoken'];
    }

    /**
     * @param mixed $userToken
     */
    public function setUserToken($userToken): void
    {
        $this->userToken = $userToken;
    }


    public function isLogin()
    {
        $userToken = Request::getInstance()->header['usertoken'] ?? false;
        if (!$userToken)
            throw new ResultException([
                'msg' => '用户未登录'
            ]);
    }
}