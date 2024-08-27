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
use app\admin\validate\user\UserGroup as UserGroupVal;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\model\user\UserGroupModel as UserGroupModel;
use Exception;
use think\response\Json;

class UserGroupController extends Controller
{

    protected string $authName = 'user.group';

    protected array $searchField = [
        'id' => '=',
        'name' => 'like',
        'pid' => '=',
        'create_time' => 'date',
        'update_time' => 'date'
    ];


    public function initialize(): void
    {
        parent::initialize();
        $this->model = new UserGroupModel();
        $this->validate = new UserGroupVal();
    }

    /**
     * @return Json
     * @throws Exception
     */

    #[Auth('list')]
    public function list(): Json
    {
        $rootNode = $this->model->select()->toArray();
        $data = $this->getTreeData($rootNode);
        return $this->success(compact('data'));
    }

    /**
     * 设置分组权限
     * @return Json
     * @throws Exception
     */
    #[Method('POST'), Auth]
    public function setGroupRule(): Json
    {
        $params = $this->request->param();
        if (!isset($params['id'])) {
            return $this->warn('请选择管理分组');
        }
        $group = $this->model->where('id', $params['id'])->findOrEmpty();
        if ($group->isEmpty()) {
            return $this->warn('用户组不存在');
        }
        $group->rules = implode(',', $params['rule_ids']);
        $group->save();
        return $this->success();
    }
}