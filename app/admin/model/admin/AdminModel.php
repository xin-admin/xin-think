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

namespace app\admin\model\admin;

use app\common\library\token\Token;
use app\common\model\admin\AdminModel as BaseAdminModel;
use app\common\model\file\FileModel;
use Exception;
use think\db\exception\DbException;
use think\model\relation\HasOne;

class AdminModel extends BaseAdminModel
{
    /**
     * @description: 模型登录
     * @param string $username 用户名
     * @param string $password 密码
     */
    public function login(string $username, string $password): array|bool
    {
        try {
            $user = $this->where('username', $username)->find();
            if (! $user) {
                $this->setErrorMsg('用户不存在');
                return false;
            }
            if (! $user['status']) {
                $this->setErrorMsg('账户已被禁用');
                return false;
            }
            // 验证密码
            if (! password_verify($password, $user['password'])) {
                $this->setErrorMsg('密码错误');
                return false;
            }
            $token = new Token();

            $data = [];
            $data['refresh_token'] = md5(random_bytes(10));
            $data['token'] = md5(random_bytes(10));
            $data['id'] = $user['id'];
            if (
                $token->set($data['token'], 'admin', $user['id'], 600)
                && $token->set($data['refresh_token'], 'admin-refresh', $user['id'], 2592000)
            ) {
                return $data;
            }
            $this->setErrorMsg('token 生成失败');
            return false;
        } catch (Exception $e) {
            $this->setErrorMsg($e->getMessage());
            return false;
        }
    }

    /**
     * 退出登录.
     * @param mixed $user_id
     */
    public function logout($user_id): bool
    {
        try {
            $user = $this->where('id', $user_id)->find();
        } catch (DbException $e) {
            $this->setErrorMsg('用户不存在');
            return false;
        }
        if (! $user) {
            $this->setErrorMsg('用户不存在');
            return false;
        }
        try {
            $token = new Token();
            $token->clear('admin', $user['id']);
            $token->clear('admin-refresh', $user['id']);
            return true;
        } catch (Exception $e) {
            $this->setErrorMsg($e->getMessage());
            return false;
        }
    }

    /**
     * @param mixed $model
     * @throws Exception
     */
    public static function onBeforeDelete($model): void
    {
        if ($model->username == 'admin') {
            // 当用户name等于"admin"时，抛出异常阻止删除
            throw new Exception('不允许删除管理员用户');
        }
    }

    /**
     * 关联用户头像表.
     */
    public function avatar(): HasOne
    {
        return $this->hasOne(FileModel::class, 'file_id', 'avatar_id')
            ->bind(['avatar_url' => 'preview_url']);
    }
}
