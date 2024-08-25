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
namespace app\admin\controller\file;

use app\admin\controller\Controller;
use app\admin\model\file\FileModel as FileModel;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\library\storage\Storage as StorageDriver;
use Exception;
use think\db\exception\DbException;
use think\response\Json;


class FileController extends Controller
{
    protected string $authName = 'file.file';


    public function initialize(): void
    {
        parent::initialize();
        $this->model = new FileModel();
    }

    protected array $searchField = [
        'group_id' => '=',
        'name' => 'like',
        'file_type' => '=',
    ];

    public function add(): Json
    {
        return $this->error('不能直接新增文件记录');
    }

    /**
     * 基础控制器查询方法
     * @return Json
     * @throws DbException
     */
    #[Method('GET'), Auth('list')]
    public function list(): Json
    {
        list($where, $paginate) = $this->buildSearch();
        $list = $this->model
            ->with($this->withModel)
            ->where($where)
            ->paginate($paginate)
            ->toArray();
        return $this->success($list);
    }

    /**
     * 基础控制器删除方法
     * @return Json
     * @throws Exception
     */
    #[Method('DELETE'), Auth('delete')]
    public function delete(): Json
    {
        $data = $this->request->param();
        if (!isset($data['ids'])) {
            return $this->error('请选择ID');
        }
        $delArr = explode(',', $data['ids']);
        // 验证文件数量
        if (count($delArr) > 15) {
            return $this->error('一次性最多删除15个文件');
        }
        foreach ($delArr as $fileId) {
            // 获取文件详情
            $fileInfo = $this->model->find($fileId);
            // 实例化存储驱动
            $storage = new StorageDriver($fileInfo['storage']);
            // 删除文件
            if (!$storage->delete($fileInfo->toArray())) {
                return $this->error('文件删除失败：' . $storage->getError());
            }
            // 标记为已删除
            $fileInfo->delete();
        }
        return $this->success('删除成功，删除了' . count($delArr) . '个文件数据');
    }
}