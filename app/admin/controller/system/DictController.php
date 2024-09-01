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
use app\admin\model\dict\DictModel;
use app\admin\validate\system\Dict as DictVal;

class DictController extends Controller
{
    protected string $authName = 'system.dict';

    protected array $searchField = [
        'id' => '=',
        'name' => 'like',
        'code' => '=',
        'create_time' => 'date',
        'update_time' => 'date',
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new DictModel();
        $this->validate = new DictVal();
    }
}
