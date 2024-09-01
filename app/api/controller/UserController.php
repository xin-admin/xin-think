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

namespace app\api\controller;

use app\admin\model\file\FileModel as UploadFileModel;
use app\api\model\UserModel;
use app\api\validate\User as UserVal;
use app\BaseController;
use app\common\attribute\Auth;
use app\common\attribute\Method;
use app\common\enum\FileType as FileTypeEnum;
use app\common\library\storage\Storage as StorageDriver;
use app\common\library\token\Token;
use app\common\model\user\UserGroupModel;
use app\common\model\user\UserMoneyLogModel;
use app\common\model\user\UserRuleModel;
use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

class UserController extends BaseController
{
    /**
     * 获取用户信息.
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[Method('GET'), Auth]
    public function getUserInfo(): Json
    {
        $info = Auth::getUserInfo();
        // 获取权限
        $model = new UserGroupModel();
        $group = $model->where('id', $info['group_id'])->findOrEmpty()->toArray();
        $where = [];
        $where[] = ['status', '=', 1];
        $where[] = ['id', 'in', $group['rules']];
        $rule_model = new UserRuleModel();
        // 权限
        $access = $rule_model->where($where)->column('key');
        // 菜单
        $where[] = ['show', '=', 1];
        $where[] = ['type', 'in', [0, 1]];
        $menus = $rule_model->where($where)->order('sort', 'desc')->select()->toArray();
        $menus = $this->getTreeData($menus);

        return $this->success(compact('info', 'access', 'menus'));
    }

    /**
     * 刷新 Token.
     * @throws Exception
     */
    public function refreshToken(): Json
    {
        $token = $this->request->header('x-user-token');
        $reToken = $this->request->header('x-user-refresh-token');
        if ($this->request->isPost() && $reToken) {
            $Token = new Token();
            $Token->delete($token);
            $user_id = $Token->get($reToken)['user_id'];
            $token = md5(random_bytes(10));
            $Token->set($token, 'user', $user_id);
            return $this->success(compact('token'));
        }
        return $this->error('请先登录！');
    }

    /**
     * 退出登录.
     */
    #[Auth]
    public function logout(): Json
    {
        $user_id = Auth::getUserId();
        $model = new UserModel();
        if ($model->logout($user_id)) {
            return $this->success('退出登录成功');
        }
        return $this->error($model->getErrorMsg());
    }

    /**
     * 头像上传接口.
     * @throws Exception
     */
    public function upAvatar(): Json
    {
        // 实例化存储驱动
        $storage = new StorageDriver('local');
        // 设置上传文件的信息
        $storage->setUploadFile('file');
        // 设置上传文件验证规则
        $storage->setValidationScene('image');
        // 执行文件上传
        if (! $storage->upload()) {
            return $this->error('图片上传失败：' . $storage->getError());
        }
        // 文件信息
        $fileInfo = $storage->getSaveFileInfo();
        // 添加文件库记录
        $model = new UploadFileModel();
        $user_id = Auth::getUserId();
        $model->add($fileInfo, FileTypeEnum::IMAGE->value, $user_id, 14, 20);
        // 图片上传成功
        return $this->success(['fileInfo' => $model->toArray()], '图片上传成功');
    }

    /**
     * 设置用户信息.
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[Method('POST'), Auth]
    public function setUserInfo(): Json
    {
        $data = $this->request->post();
        $validate = new UserVal();
        $model = new UserModel();
        $result = $validate->scene('set')->check($data);
        if (! $result) {
            return $this->warn($validate->getError());
        }
        $user = $model->where('id', Auth::getUserId())->find();
        $save = $user->allowField(['username', 'nickname', 'gender', 'avatar_id', 'mobile', 'email'])->save($data);
        if ($save) {
            return $this->success('更新成功');
        }
        return $this->error('更新失败');
    }

    /**
     * 设置密码
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[Method('POST'), Auth]
    public function setPassword(): Json
    {
        $data = $this->request->post();
        $validate = new UserVal();
        $model = new UserModel();
        $result = $validate->scene('set_pwd')->check($data);
        if (! $result) {
            return $this->warn($validate->getError());
        }
        $user_id = Auth::getUserId();
        $user = $model->where('id', $user_id)->find();
        if ($user->save([
            'password' => password_hash($data['newPassword'], PASSWORD_DEFAULT),
        ])) {
            return $this->success('更新成功');
        }
        return $this->error('更新失败');
    }

    /**
     * 获取用户余额记录.
     * @throws DbException
     */
    #[Method('GET'), Auth]
    public function getMoneyLog(): Json
    {
        $user_id = Auth::getUserId();
        $params = $this->request->get();
        $paginate = [
            'list_rows' => $params['pageSize'] ?? 10,
            'page' => $params['current'] ?? 1,
        ];
        $list = (new UserMoneyLogModel())
            ->where('user_id', $user_id)
            ->paginate($paginate)
            ->toArray();
        return $this->success($list);
    }
}
