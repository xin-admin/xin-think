<?php
/*
 *  +----------------------------------------------------------------------
 *  | XinAdmin [ A Full stack framework ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: 小刘同学 <2302563948@qq.com>
 *  +----------------------------------------------------------------------
 */

namespace app\admin\controller\user;

use app\admin\controller\Controller;
use app\admin\validate\User as UserVal;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\model\user\UserModel;
use think\db\exception\DbException;
use think\response\Json;

class UserController extends Controller
{
    protected array $withModel = ['avatar'];

    protected string $authName = 'user';

    protected array $searchField = [];

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new UserModel();
        $this->validate = new UserVal();
    }

    /**
     * 搜索用户.
     * @throws DbException
     */
    #[Method('GET'), Auth]
    public function vagueSearch(): Json
    {
        $value = $this->request->param('search');
        if ($value) {
            $data = $this->model
                ->where('id|username|nickname|mobile', 'like', "%{$value}%")
                ->order('id', 'desc')
                ->paginate(10)
                ->toArray();
        } else {
            $data = $this->model
                ->order('id', 'desc')
                ->paginate(10)
                ->toArray();
        }
        return $this->success($data);
    }
}
