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
use app\admin\model\setting\SettingGroupModel;
use app\admin\model\setting\SettingModel;
use app\admin\validate\system\Setting as SettingVal;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

class SettingController extends Controller
{
    protected array $searchField = [
        'group_id' => '=',
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new SettingModel();
        $this->validate = new SettingVal();
    }

    /**
     * 基础控制器查询方法.
     * @throws DbException
     */
    #[Method('GET'), Auth('list')]
    public function list(): Json
    {
        $group_id = $this->request->param('group_id');
        if (! $group_id) {
            return $this->warn('请选择设置分组');
        }
        $list = $this->model
            ->with($this->withModel)
            ->append(['defaultData'])
            ->where('group_id', '=', $group_id)
            ->order('sort', 'desc')
            ->select()
            ->toArray();
        return $this->success($list);
    }

    /**
     * 保存设置.
     */
    #[Method('POST'), Auth('add')]
    public function saveSetting(): Json
    {
        $data = $this->request->param();
        if (! isset($data['group_id'])) {
            return $this->warn('请选择分组');
        }
        foreach ($data as $key => $value) {
            $setting = $this->model->where('group_id', $data['group_id'])->where('key', $key)->findOrEmpty();
            if ($setting->isEmpty()) {
                continue;
            }
            $setting->values = $value;
            $setting->save();
        }
        return $this->success('保存成功！');
    }

    /**
     * 新增分组.
     */
    #[Method('POST'), Auth('addGroup')]
    public function addGroup(): Json
    {
        $params = $this->request->param();
        if (! $this->validate->scene('addGroup')->check($params)) {
            return $this->error($this->validate->getError());
        }
        $settingGroupModel = new SettingGroupModel();
        $settingGroupModel->save($params);
        return $this->success('ok');
    }

    /**
     * 查询设置分组.
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[Method('GET'), Auth('querySettingGroup')]
    public function querySettingGroup(): Json
    {
        $settingGroupModel = new SettingGroupModel();
        $rootGroup = $settingGroupModel->field('id,key,title as label')->select()->toArray();
        return $this->success($rootGroup);
    }
}
