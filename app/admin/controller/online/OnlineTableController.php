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
namespace app\admin\controller\online;

use app\admin\controller\Controller;
use app\admin\model\OnlineTableModel as OnlineTableModel;
use app\admin\validate\online\OnlineTable as OnlineTableVal;
use app\BaseController;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\library\Crud;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Validate;
use think\response\Json;

class OnlineTableController extends Controller
{
    protected array $searchField = [
        'table_id' => '=',
        'table_name' => 'like',
        'create_time' => 'date',
        'update_time' => 'date'
    ];

    protected string $authName = 'online.table';

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new OnlineTableModel();
        $this->validate = new OnlineTableVal();
    }

    /**
     * 基础控制器新增方法
     * @return Json
     */
    #[Auth('add'), Method('POST')]
    public function add(): Json
    {
        if(!$this->model) return $this->warn('当前控制器未设置模型');
        if(!$this->validate) return $this->warn('当前控制器未设置验证器');
        $data = $this->request->post();
        if (!$this->validate->scene('add')->check($data)) {
            return $this->error($this->validate->getError());
        }
        $this->model->save([
            'table_name' => $data['table_name'],
            'columns' => '{}',
            'crud_config' => '{}',
            'table_config' => '{}',
            'describe' => $data['describe'] ?? ''
        ]);
        return $this->success('ok');
    }

    /**
     * 保存更改
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[Auth('saveData'), Method('POST')]
    public function saveData(): Json
    {
        $data = request()->post();
        $validate = Validate::rule([
            'id' => 'require',
            'columns' => 'require',
            'table_config' => 'require',
            'crud_config' => 'require'
        ]);
        if (!$validate->check($data)) {
            return $this->warn($validate->getError());
        }
        $data['id'] = (int)$data['id'];
        $online = $this->model->where('id', $data['id'])->find();
        if ($online) {
            $online->columns = $data['columns'];
            $online->table_config = $data['table_config'];
            $online->crud_config = $data['crud_config'];
            $online->save();
            return $this->success('保存成功', $data);
        }
        return $this->error('保存失败');
    }

    /**
     * 获取 CRUD 数据
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[Auth('getData'), Method('GET')]
    public function getData(): Json
    {
        $id = request()->param('id');
        if (!$id) {
            return $this->warn('id不存在');
        }

        $data = $this->model->field('columns,table_config,crud_config')->where('id', $id)->find();
        if (!$data) {
            return $this->warn('表单不存在');
        }
        return $this->success(compact('data'));

    }

    /**
     * CRUD
     * @return Json
     */
    #[Auth('crud'), Method('POST')]
    public function crud(): Json
    {
        if(env('WEB_NAME') && env('WEB_NAME') == 'xin_test'){
            return $this->warn('演示站已禁止此操作');
        }
        $data = request()->post();
        $validate = Validate::rule([
            'id' => 'require',
            'columns' => 'require',
            'table_config' => 'require',
            'crud_config' => 'require'
        ]);
        if (!$validate->check($data)) {
            return $this->warn($validate->getError());
        }

        $crud = new Crud($data);
        if($crud->generate()) {
            return $this->success('ok');
        }
        return $this->error($crud->getErrorMsg());

    }

}