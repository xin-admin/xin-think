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

namespace app\admin\validate\file;

use think\Validate;

class Group extends Validate
{
    protected $rule = [
        'name' => 'require',
        'parent_id' => 'require',
        'sort' => 'max:10|number',
    ];

    protected $scene = [
        // 新增
        'add' => ['name', 'parent_id', 'sort'],
    ];
}
