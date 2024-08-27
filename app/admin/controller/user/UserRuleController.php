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
use app\admin\validate\user\UserRule as UserRuleVal;
use app\common\attribute\Auth;
use app\common\model\user\UserRuleModel as UserRuleModel;
use Exception;
use think\response\Json;

class UserRuleController extends Controller
{

    protected array $searchField = [
        'id' => '=',
        'pid' => '=',
        'type' => '=',
        'name' => 'like',
        'key' => '=',
        'create_time' => 'date',
        'update_time' => 'date'
    ];

    protected string $authName = 'user.rule';

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new UserRuleModel();
        $this->validate = new UserRuleVal();
    }


    /**
     * @return Json
     * @throws Exception
     */
    #[Auth('list')]
    public function list(): Json
    {
        $rootNode = $this->model->order('sort', 'desc')->select()->toArray();
        $data = $this->getTreeData($rootNode);
        return $this->success(compact('data'));
    }

    /**
     * 获取菜单节点
     * @return Json
     * @throws Exception
     */
    #[Auth('list')]
    public function getRulePid(): Json
    {
        $rootNode = $this->model
            ->where('type', '<>', '2')
            ->order('sort', 'desc')
            ->select()
            ->toArray();
        $data = $this->getTreeData($rootNode);
        return $this->success(compact('data'));
    }
}