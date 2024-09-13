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
namespace app\admin\controller;

use app\admin\model\admin\AdminRuleModel as AdminRuleModel;
use app\admin\validate\AdminRule as AdminRuleVal;
use app\common\attribute\Auth;
use Exception;
use think\db\exception\DbException;
use think\response\Json;
use app\common\attribute\Monitor;

class AdminRuleController extends Controller
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

    protected string $authName = 'admin.rule';


    public function initialize(): void
    {
        parent::initialize();
        $this->model = new AdminRuleModel();
        $this->validate = new AdminRuleVal();
    }

    /**
     * 查询列表
     * @return Json
     * @throws DbException
     */
    #[Auth('list')]
    public function list(): Json
    {
        trace('1231231');
        $rootNode = $this->model->order('sort', 'desc')->select()->toArray();
        $data = $this->getTreeData($rootNode);
        return $this->success(compact('data'));
    }

    /**
     * 获取菜单节点
     * @return Json
     * @throws Exception
     */
    #[Auth]
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