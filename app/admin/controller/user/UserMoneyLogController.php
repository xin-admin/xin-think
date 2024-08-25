<?php
// +----------------------------------------------------------------------
// | XinAdmin [ A Full stack framework ]
// +----------------------------------------------------------------------
// | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小刘同学 <2302563948@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller\user;

use app\admin\controller\Controller;
use app\admin\validate\MoneyLog as MoneyLogVal;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\model\user\UserModel;
use app\common\model\user\UserMoneyLogModel as MoneyLogModel;
use think\response\Json;

class UserMoneyLogController extends Controller
{

    protected string $authName = 'user.moneyLog';


    public function initialize(): void
    {
        parent::initialize();
        $this->model = new MoneyLogModel();
        $this->validate = new MoneyLogVal();
    }

    #[Method('GET'), Auth('list')]
    public function list(): Json
    {
        list($where, $paginate) = $this->buildSearch();
        $list = $this->model
            ->with('user')
            ->where($where)
            ->paginate($paginate)
            ->toArray();
        return $this->success($list);
    }

    #[Method('POST'), Auth('add')]
    public function add(): Json
    {
        $data = $this->request->post();
        if (!$this->validate->scene('add')->check($data)) {
            return $this->error($this->validate->getError());
        }
        $userModel = new UserModel();
        if ($data['money'] > 0) {
            $userModel->setIncMoney($data['id'], abs($data['money']), $data['remark'], '0', []);
        } else {
            $userModel->setDecMoney($data['id'], abs($data['money']), $data['remark'], '0', []);
        }
        return $this->success();

    }

}