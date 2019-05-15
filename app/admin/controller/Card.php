<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/5/14
 * Time: 下午2:45
 */

namespace app\admin\controller;


use app\admin\model\CardModel;
use app\admin\model\UserModel;
use seal\exception\ResultException;
use seal\Request;

class Card extends Base
{
    protected $beforeAction = ['isLogin' => ['create']];

    public function create(CardModel $cardModel, Request $request)
    {
        $res = $cardModel->getDb()->where('uid='
            . "'" . $this->getUserToken()
            . "'" . 'AND card_type=' . CardModel::ID_CARD)->find();
        if ($res) {
            throw new ResultException([
                'msg' => '您已经绑定了一张此类证件'
            ]);
        }
        try {
            $cardModel->uid = $this->getUserToken();
            $cardModel->card_no = $request->card_no;
            $cardModel->card_type = $request->card_type;
            $cardModel->create();
        } catch (\Exception $e) {
            throw new ResultException([
                'msg' => '参数错误'
            ]);
        }
        return $this->success();
    }

    public function read(CardModel $cardModel, Request $request)
    {
        $res = $cardModel->getDb()->where('card_no=' . $request->card_no)->find();
        if (!$res)
            throw new ResultException([
                'msg' => '证件号码不存在'
            ]);

        $user = UserModel::get($res['uid']);
        $data['mobile'] = $res[''];
        return $this->success();
    }
}