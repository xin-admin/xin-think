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

namespace app\api\validate;

use app\common\attribute\Auth;
use think\Validate;

class User extends Validate
{
    protected $rule = [
        'id' => 'require|max:10|int',
        'username' => 'require|min:4|alphaDash',
        'password' => 'require|min:4|alphaDash',
        'autoLogin' => 'boolean',
        'mobile' => 'require|mobile',
        'captcha' => 'require|max:4',
        'nickname' => 'require',
        'sex' => 'max:1|string',
        'email' => 'require|email',
        'gender' => 'max:1|string',
        'avatar' => 'url',
        'rePassword' => 'require|alphaDash|confirm:password',
        'oldPassword' => 'require|alphaDash|oldPassword',
        'newPassword' => 'require|alphaDash',
        'regType' => 'require',
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.max' => '用户名最多不能超过10个字符',
        'username.alphaDash' => '用户名只能为字母和数字、下划线_及破折号-',

        'password.require' => '密码不能为空',
        'password.max' => '密码最多不能超过10个字符',
        'password.alphaDash' => '用户名只能为字母和数字、下划线_及破折号-',

        'mobile.require' => '手机号不能为空',
        'mobile.mobile' => '手机号格式错误',

        'captcha.require' => '验证码不能为空',
        'captcha.max' => '验证码最多不能超过4个字符',
    ];

    protected $scene = [
        // 账号密码登录
        'account' => ['username', 'password'],
        // 邮箱登录
        'email' => ['email', 'captcha'],
        // 手机号登录
        'phone' => ['mobile', 'captcha'],
        // 注册会员
        'reg' => ['username', 'nickname', 'password', 'rePassword', 'regType'],

        'set' => ['username', 'nickname', 'gender', 'email', 'avatar', 'mobile'],

        'set_pwd' => ['oldPassword', 'newPassword', 'rePassword'],
    ];

    // 自定义验证规则
    protected function rePassword($value, $rule, $data = []): bool|string
    {
        return $value == $data['newPassword'] ? true : '两次密码不同';
    }

    // 自定义验证规则
    protected function repeatPassword($value, $rule, $data = []): bool|string
    {
        return $value == $data['password'] ? true : '两次密码不同';
    }

    protected function oldPassword($value): bool|string
    {
        $user = Auth::getUserInfo();
        if (! password_verify($value, $user['password'])) {
            return '旧密码不正确';
        }
        return true;
    }
}
