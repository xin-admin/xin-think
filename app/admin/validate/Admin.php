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

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'id' => 'require|max:10|number',
        'username' => 'require|max:10|alphaDash',
        'password' => 'require|max:10|graph',
        'autoLogin' => 'boolean',
        'mobile' => 'require|mobile',
        'captcha' => 'require|max:4',
        'nickname' => 'require',
        'sex' => 'max:1|string',
        'rePassword' => 'require|confirm:password',
        'group_id' => 'require|number',
        'status' => 'require|number',
    ];

    protected $scene = [
        // 账号密码登录
        'account' => ['username', 'password'],
        // 手机号登录
        'phone' => ['mobile', 'captcha'],
        // 新增管理员
        'add' => ['username', 'nickname', 'password', 'email', 'mobile', 'rePassword', 'group_id', 'status'],
        // 修改密码
        'updatePassword' => ['id', 'password', 'rePassword'],
        // 修改信息
        'updateAdmin' => ['username', 'mobile', 'nickname', 'email', 'avatar_id'],
    ];

    // edit 验证场景定义
    public function sceneEdit(): Admin
    {
        return $this->only(['id', 'mobile', 'nickname', 'sex', 'group_id', 'status'])
            ->remove('nickname', 'require')
            ->remove('group_id', 'require')
            ->remove('status', 'require')
            ->remove('mobile', 'require');
    }
}
