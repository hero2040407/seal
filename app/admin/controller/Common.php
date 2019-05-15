<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/5/9
 * Time: 上午10:20
 */

namespace app\admin\controller;


use app\admin\model\UserModel;
use seal\Http;
use seal\Request;

class Common extends Base
{
    private static $appid = 'wx0572d895e95dd0c6';
    private static $secret = 'c9a5fd354b1c26007053bddf975d5aa9';

    public function login(UserModel $user, Request $request)
    {
        $domain = 'api.weixin.qq.com';
        $path = '/sns/jscode2session?appid='
            . self::$appid . '&secret=' . self::$secret . '&js_code='
            . $request->code . '&grant_type=authorization_code';
        $res = Http::get($domain, $path, 443, true);

        $user_info = $user->getDb()->where('openid=' . "'" . $res->openid . "'")->find();
        if ($user_info) {
            return $this->success($user_info['id']);
        }
        $user->setId();
        $user->openid = $res->openid;
        $user->create();

        return $this->success($user->id);
    }
}