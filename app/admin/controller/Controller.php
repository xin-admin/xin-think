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

use app\BaseController;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\model\BaseModel;
use think\db\exception\DbException;
use think\response\Json;
use think\Validate;

class Controller extends BaseController
{

    /**
     * 可直接使用的数据库术语
     * @var array|string[]
     */
    protected array $sqlTerm = ['=', '>', '<>', '<', '>=', '<='];

    /**
     * 关联预载入模型
     * @var array
     */
    protected array $withModel = [];

    /**
     * 快速查询字段
     * @var array
     */
    protected array $quickSearchField = [];

    /**
     * 查询字段
     * @var array
     */
    protected array $searchField = [];

    /**
     * 当前控制器模型
     * @var BaseModel|null
     */
    protected null|BaseModel $model = null;

    /**
     * 验证器
     * @var Validate|null
     */
    protected null|Validate $validate = null;

    /**
     * 构建查询方法
     * @return array
     */
    protected function buildSearch(): array
    {
        $params = $this->request->get();

        // 构建分页
        $paginate = [
            'list_rows' => $params['pageSize'] ?? 10,
            'page' => $params['current'] ?? 1,
        ];
        $pk = $this->model->getPk();
        $order = [$pk => 'desc'];
        $where = [];

        // 构建排序
        if (isset($params['sorter']) && $params['sorter']) {
            $sorter = json_decode($params['sorter'], true);
            if (count($sorter) > 0) {
                $sorterKey = array_keys($sorter)[0];
                $sorterType = $sorter[$sorterKey] == 'ascend' ? 'asc' : 'desc';
                $order = [$sorterKey => $sorterType];
            }
        }

        // 快速搜索
        $modelTable = strtolower($this->model->getTable());
        $alias[$modelTable] = parse_name(basename(str_replace('\\', '/', get_class($this->model))));
        $tableAlias = $alias[$modelTable] . '.';
        if (isset($params['keyword']) && $params['keyword'] != '') {
            $quickSearchArr = $this->quickSearchField;
            foreach ($quickSearchArr as $k => $v) {
                $quickSearchArr[$k] = stripos($v, ".") === false ? $tableAlias . $v : $v;
            }
            $where[] = [implode("|", $quickSearchArr), "LIKE", '%' . str_replace('%', '\%', $params['keyword']) . '%'];
        }

        // 构建筛选
        if (isset($params['filter']) && $params['filter'] != '') {
            $filter = json_decode($params['filter'], true);
            foreach ($filter as $k => $v) {
                if (!$v) {
                    continue;
                }
                $where[] = [$k, 'in', $v];
            }
        }

        // 构建查询
        foreach ($this->searchField as $key => $op) {
            if (isset($params[$key]) && $params[$key] != '') {
                if (in_array($op, $this->sqlTerm)) {
                    $where[] = [$key, $op, $params[$key]];
                    continue;
                }
                if ($op == 'like') {
                    $where[] = [$key, $op, '%' . $params[$key] . '%'];
                    continue;
                }
                if ($op == 'date') {
                    $start = strtotime($params[$key]);
                    $end = strtotime('+1 day', $start);
                    $where[] = [$key, '>=', $start];
                    $where[] = [$key, '<=', $end];
                    continue;
                }
            }
        }
        return [$where, $paginate, $order];
    }

    /**
     * 基础控制器查询方法
     * @return Json
     * @throws DbException
     */
    #[Auth('list'), Method('GET')]
    public function list(): Json
    {
        if(!$this->model) return $this->warn('当前控制器未设置模型');
        list($where, $paginate, $order) = $this->buildSearch();
        $list = $this->model
            ->with($this->withModel)
            ->where($where)
            ->order($order)
            ->paginate($paginate)
            ->toArray();
        return $this->success($list);
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
        $this->model->save($data);
        return $this->success('ok');
    }

    /**
     * 基础控制器编辑方法
     * @return Json
     */
    #[Auth('edit'), Method('PUT')]
    public function edit(): Json
    {
        if(!$this->model) return $this->warn('当前控制器未设置模型');
        if(!$this->validate) return $this->warn('当前控制器未设置验证器');
        $data = $this->request->param();
        if (!$this->validate->scene('edit')->check($data)) {
            return $this->warn($this->validate->getError());
        }
        $this->model->update($data);
        return $this->success('ok');
    }

    /**
     * 基础控制器删除方法
     * @return Json
     */
    #[Auth('delete'), Method('DELETE')]
    public function delete(): Json
    {
        if(!$this->model) return $this->warn('当前控制器未设置模型');
        $data = $this->request->param();
        if (!isset($data['ids'])) {
            return $this->error('请选择ID');
        }
        $delArr = explode(',', $data['ids']);

        $delNum = $this->model->destroy($delArr);
        if ($delNum != 0) {
            return $this->success('删除成功，删除了' . $delNum . '条数据');
        } else {
            return $this->warn('没有删除任何数据');
        }
    }


}