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

namespace app\admin\validate\user;

use think\Validate;

class UserGroup extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...].
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'pid' => 'number|max:2',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'.
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请输入名字',
        'pid.number' => '父ID格式错误',
    ];
}
