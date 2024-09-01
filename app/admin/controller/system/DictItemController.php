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

namespace app\admin\controller\system;

use app\admin\controller\Controller;
use app\admin\model\dict\DictItemModel;
use app\admin\model\dict\DictModel;
use app\admin\validate\system\DictItem as DictItemVal;
use app\common\attribute\Auth;
use think\response\Json;

class DictItemController extends Controller
{
    protected string $authName = 'system.dict.item';

    protected array $searchField = [
        'name' => 'like',
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new DictItemModel();
        $this->validate = new DictItemVal();
    }

    #[Auth('list')]
    public function list(): Json
    {
        [$where, $paginate, $order] = $this->buildSearch();
        $params = $this->request->get();
        if (! isset($params['dictId'])) {
            return $this->warn('字典ID不存在');
        }

        $list = $this->model
            ->where('dict_id', $params['dictId'])
            ->where($where)
            ->order($order)
            ->paginate($paginate)
            ->toArray();
        return $this->success($list);
    }

    public function dictList(): Json
    {
        $dict = (new DictModel())->with('dictItems')->visible(['dictItems' => ['label', 'value', 'status']])->select()->toArray();
        return $this->success($dict);
    }
}
