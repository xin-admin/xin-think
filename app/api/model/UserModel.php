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
namespace app\api\model;

use app\common\library\token\Token;
use app\common\model\user\UserModel as BaseUserModel;
use Exception;

class UserModel extends BaseUserModel
{

    /**
     * 用户名密码登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return bool | array
     */
    public function login(string $username, string $password): bool | array
    {
        try {
            $user = $this->where('username',$username)->find();
            if(!$user) {
                $this->setErrorMsg('用户不存在');
                return false;
            }
            // 验证密码
            if(!password_verify($password,$user['password'])){
                $this->setErrorMsg('密码错误');
                return false;
            }
            return $this->getToken($user['id']);
        }catch(Exception $e){
            $this->setErrorMsg($e->getMessage());
            return false;
        }
    }

    /**
     * 邮箱登录 | 未存在用户自动注册
     * @param $mail
     * @return bool|array
     */
    public function mailLogin($mail): bool|array
    {
        $userInfo = $this->where('email',$mail)->findOrEmpty();
        if($userInfo->isEmpty()) {
            $user = self::create([
                'username' => 'uid'.time(),
                'nickname' => substr_replace($mail,'****',3,4),
                'password' => password_hash('',PASSWORD_DEFAULT),
                'mobile' => '',
                'email' => $mail
            ]);
            return $this->getToken($user['id']);
        }else {
            return $this->getToken($userInfo['id']);
        }
    }

    /**
     * 用户注册
     * @param $data
     * @return bool|array
     */
    public function register($data): bool|array
    {
        try {
            $user = $this->where('username',$data['username'])->find();
            if($user) {
                $this->setErrorMsg('用户名被占用');
                return false;
            }
            $user = $this->where('email',$data['email'])->find();
            if($user) {
                $this->setErrorMsg('注册邮箱被占用');
                return false;
            }
            $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
            $data['mobile'] = $data['mobile'] ?? '';
            $data['nickname'] = $data['nickname'] ?? $data['email'];

            $user = self::create($data,['username','nickname','password','mobile','email']);
            return $this->getToken($user['id']);
        }catch(Exception $e){
            $this->setErrorMsg('系统错误'.$e->getMessage());
            return false;
        }
    }

    /**
     * 获取 Token
     * @param int $user_id
     * @return array|false
     */
    private function getToken(int $user_id): array | false
    {
        try {
            $token = new Token();
            $data = [];
            $data['refresh_token'] =  md5(random_bytes(10));
            $data['token'] =  md5(random_bytes(10));
            $data['id'] = $user_id;
            if(
                $token->set($data['token'],'user',$user_id,600) &&
                $token->set($data['refresh_token'],'user-refresh',$user_id,2592000)
            ) {
                return $data;
            } else {
                $this->setErrorMsg('token 生成失败');
                return false;
            }
        }catch(\Exception $e){
            $this->setErrorMsg($e->getMessage());
            return false;
        }
    }

    /**
     * 退出登录
     * @param $user_id
     * @return bool
     */
    public function logout($user_id): bool
    {
        try {
            $user = $this->where('id',$user_id)->find();
            if(!$user) {
                $this->setErrorMsg('用户不存在');
                return false;
            }
            $token = new Token;
            $token->clear('user',$user['id']);
            $token->clear('user-refresh',$user['id']);
            return true;
        }catch(\Exception $e){
            $this->setErrorMsg($e->getMessage());
            return false;
        }
    }
}